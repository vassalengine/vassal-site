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
        items = []          # List to store matching artifacts
        total_count = 0     # Total number of matching artifacts
        api_page = 1        # Current API page number
        found_matches = False # Flag to indicate if we've found any matches
        checked_next_page = False # Flag to indicate if we've checked the next page

        while True:
            # Fetch artifacts from the API page by page
            data = get_page(api_page, 100)
            artifacts = data.get('artifacts', [])
            if not artifacts:
                break  # Exit loop if no more artifacts are found

            # Check current page for matches
            current_page_matches = []
            for artifact in artifacts:
                build_id = extract_build_id(artifact['name'])
                if build_id == build_id_filter:
                    current_page_matches.append(artifact)

            if current_page_matches:
                found_matches = True
                items.extend(current_page_matches)

                # If we have enough items for the current page, stop
                if len(items) >= PER_PAGE:
                    break

                # If we haven't checked the next page yet, do so
                if not checked_next_page:
                    checked_next_page = True
                    api_page += 1
                    continue
                else:
                    # We've already checked next page, so stop
                    break
            else:
                # No matches in current page
                if found_matches:
                    # We had matches before but none in this page, stop
                    break
                else:
                    # No matches found yet, continue to next page
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