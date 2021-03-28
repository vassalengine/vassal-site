#!/bin/python3 

from flask import Flask, app, redirect, render_template

import requests


app = Flask(__name__)
app.config.from_object('config.Config')

per_page = app.config['PER_PAGE']
token = app.config['TOKEN']
user = app.config['USER']
repo = app.config['REPO']
api_url = f'https://api.github.com/repos/{user}/{repo}/actions/artifacts'


@app.route('/builds/<int:page>')
def show_builds(page):
    r = requests.get(
        api_url,
        params={'per_page': per_page, 'page': page},
        headers={'Authorization': f'token {token}'}
    )

    j = r.json()
    total_count = j['total_count']
    items = j['artifacts']

    return render_template(
        'list.html',
        items=items,
        page=page,
        per_page=per_page,
        total_count=total_count
    )


@app.route('/build/<build_id>')
def request_build(build_id):
    r = requests.get(
        f'{api_url}/{build_id}/zip',
        headers={'Authorization': f'token {token}'},
        allow_redirects=False
    )
    return redirect(r.headers['Location'])


if __name__ == '__main__':
    app.run()
