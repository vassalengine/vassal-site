#!/usr/bin/python3

import math
import traceback
import requests
import re

from flask import Flask, redirect, request, render_template

app = Flask(__name__)
app.config.from_object('config.Config')

PER_PAGE = app.config['PER_PAGE']
TOKEN = app.config['TOKEN']
USER = app.config['USER']
REPO = app.config['REPO']

API_URL = f'https://api.github.com/repos/{USER}/{REPO}/actions/artifacts'

@app.errorhandler(Exception)
def handle_error(err):
    tb = ''.join(traceback.format_exception(type(err), err, err.__traceback__))
    return render_template('error.html', err=tb), 500

def get_page(page, per_page):
    return requests.get(
        API_URL,
        params={'per_page': per_page, 'page': page},
        headers={
            'Authorization': f'token {TOKEN}',
            'Accept': 'application/vnd.github.v3+json'
        }
    ).json()

def extract_build_id(artifact_name):
    match = re.search(r'VASSAL-\d+\.\d+\.\d+-SNAPSHOT-([^-]+)-', artifact_name)
    if match:
        return match.group(1)
    match = re.search(r'VASSAL-\d+\.\d+\.\d+-SNAPSHOT-([^-]+)\.(exe|zip|dmg|tar\.bz2)', artifact_name)
    if match:
        return match.group(1)
    return None

@app.route('/')
@app.route('/list')
def show_builds():
    page = int(request.args.get('page', 1))
    filter_term = request.args.get('filter')
    build_id_filter = request.args.get('build')

    if build_id_filter:
        items = []
        total_count = 0
        api_page = 1
        found_build = False

        while True:
            data = get_page(api_page, 100)
            artifacts = data.get('artifacts', [])
            if not artifacts:
                break

            for artifact in artifacts:
                build_id = extract_build_id(artifact['name'])
                if build_id == build_id_filter:
                    found_build = True
                    items.append(artifact)
                    if len(items) >= PER_PAGE:
                        total_count = len(items)
                        break
                elif found_build and build_id != build_id_filter:
                    break

            if len(items) >= PER_PAGE:
                break
            if found_build:
                break
            api_page += 1

        if filter_term:
            items = [artifact for artifact in items if filter_term.lower() in artifact['name'].lower()]
        total_count = len(items)
        total_pages = math.ceil(total_count / PER_PAGE) if PER_PAGE > 0 else 1
    else:
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
