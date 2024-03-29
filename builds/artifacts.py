#!/usr/bin/python3

import math
import traceback

from flask import Flask, app, redirect, request, render_template

import requests


app = Flask(__name__)
app.config.from_object('config.Config')

PER_PAGE = app.config['PER_PAGE']
TOKEN = app.config['TOKEN']
USER = app.config['USER']
REPO = app.config['REPO']

API_URL = f'https://api.github.com/repos/{USER}/{REPO}/actions/artifacts'


@app.errorhandler(Exception)
def handle_error(err):
    tb = ''.join(traceback.format_exception(err))
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


@app.route('/')
@app.route('/list')
def show_builds():
    page = int(request.args.get('page', 1))
    match = request.args.get('filter')
    j = get_page(page, PER_PAGE)
    total_count = j['total_count']
    items = j['artifacts']

    return render_template(
       'list.html',
        items=items if match is None else [i for i in items if match in i['name']],
        page=page,
        per_page=PER_PAGE,
        total_count=total_count,
        match=match
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
