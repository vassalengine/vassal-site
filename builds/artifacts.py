#!/usr/bin/python3

import math
import traceback
import requests
import re

from flask import Flask, redirect, request, render_template

app = Flask(__name__)
app.config.from_object('config.Config')

PER_PAGE = app.config['PER_PAGE']  # Number of items to display per page
TOKEN = app.config['TOKEN']        # GitHub API token
USER = app.config['USER']          # GitHub username
REPO = app.config['REPO']          # GitHub repository name

API_URL = f'https://api.github.com/repos/{USER}/{REPO}/actions/artifacts'  # GitHub API URL for artifacts

@app.errorhandler(Exception)
def handle_error(err):
    """Handles unhandled exceptions and renders an error page."""
    tb = ''.join(traceback.format_exception(type(err), err, err.__traceback__))
    return render_template('error.html', err=tb), 500

def get_page(page, per_page):
    """Fetches a page of artifacts from the GitHub API."""
    return requests.get(
        API_URL,
        params={'per_page': per_page, 'page': page},
        headers={
            'Authorization': f'token {TOKEN}',
            'Accept': 'application/vnd.github.v3+json'
        }
    ).json()

def extract_build_id(artifact_name):
    """Extracts the build ID from the artifact name using a regular expression."""
    match = re.search(r'VASSAL-\d+\.\d+\.\d+-(?:SNAPSHOT-)?([^-]+)-', artifact_name)
    return match.group(1) if match else None

@app.route('/')
@app.route('/list')
def show_builds():
    """Displays a list of builds, with filtering and pagination."""
    page = int(request.args.get('page', 1))         # Current page number
    filter_term = request.args.get('filter')      # Filter term for artifact names
    build_id_filter = request.args.get('build')    # Specific build ID to filter

    if build_id_filter:
        # Logic for filtering by a specific build ID
        items = []          # List to store matching artifacts
        total_count = 0     # Total number of matching artifacts
        api_page = 1        # Current API page number
        found_build = False # Flag to indicate if the build ID has been found

        while True:
            # Fetch artifacts from the API page by page
            data = get_page(api_page, 100)
            artifacts = data.get('artifacts', [])
            if not artifacts:
                break  # Exit loop if no more artifacts are found

            for i, artifact in enumerate(artifacts):
                build_id = extract_build_id(artifact['name'])
                if build_id == build_id_filter:
                    # If the artifact's build ID matches the filter
                    found_build = True
                    items.append(artifact)
                    if len(items) >= PER_PAGE:
                        # If we have enough items for the current page, stop fetching
                        total_count = len(items)
                        break
                elif found_build and build_id != build_id_filter:
                    # If we've found the build ID but the current artifact has a different build ID,
                    # perform a lookahead to ensure we're not prematurely ending the build group.
                    lookahead = artifacts[i:i + 10]
                    if not any(extract_build_id(la['name']) == build_id_filter for la in lookahead):
                        # If the build ID is not found in the lookahead, stop fetching
                        break
            if len(items) >= PER_PAGE:
                break
            if found_build:
                break
            api_page += 1

        if filter_term:
            # Apply name filter if provided
            items = [artifact for artifact in items if filter_term.lower() in artifact['name'].lower()]
        total_count = len(items)
        total_pages = math.ceil(total_count / PER_PAGE) if PER_PAGE > 0 else 1
    else:
        # Logic for displaying all builds (or filtered by name)
        j = get_page(page, PER_PAGE)
        total_count = j['total_count']
        items = j['artifacts']
        if filter_term:
            items = [i for i in items if filter_term.lower() in i['name']]
        total_pages = math.ceil(total_count / PER_PAGE) if PER_PAGE > 0 else 1

    return render_template(
        'list.html',
        items=items,
        page=page,
        per_page=PER_PAGE,
        total_count=total_count,
        match=filter_term,
        build=build_id_filter,
        total_pages=total_pages
    )

@app.route('/build/<build_id>')
def request_build(build_id):
    """Redirects to the download URL for a specific build artifact."""
    r = requests.get(
        f'{API_URL}/{build_id}/zip',
        headers={
            'Authorization': f'token {TOKEN}',
            'Accept': 'application/vnd.github.v3+json'
        },
        allow_redirects=False
    )
    return redirect(r.headers['Location'])

if __name__ == '__main__':
    app.run()