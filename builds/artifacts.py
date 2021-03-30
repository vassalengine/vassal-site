#!/bin/python3 

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

#API_URL = f'https://api.github.com/repos/{USER}/{REPO}/actions/artifacts'
API_URL = 'https://api.github.com/repos/{USER}/{REPO}/actions/artifacts'.format(USER=USER, REPO=REPO)


@app.errorhandler(Exception)
def handle_error(err):
    tb = ''.join(traceback.format_exception(etype=type(err), value=err, tb=err.__traceback__))
    return render_template('error.html', err=tb), 500


def get_page(page, per_page):
    return requests.get(
        API_URL,
        params={'per_page': per_page, 'page': page},
        headers={
#            'Authorization': f'token {TOKEN}'
            'Authorization': 'token ' + TOKEN,
            'Accept': 'application/vnd.github.v3+json'
        }
    ).json()


@app.route('/')
@app.route('/list')
def show_builds():
    page = request.args.get('page', 1)

    if page == 'all':
        j = get_page(1, PER_PAGE)
        total_count = j['total_count']
        items = j['artifacts']

        for p in range(2, math.ceil(total_count / PER_PAGE) + 1):
            items += get_page(p, PER_PAGE)['artifacts']

        return render_template(
            'list.html',
            items=items,
            page=1,
            per_page=total_count,
            total_count=total_count
        )

    else:
        page = int(page)
        j = get_page(page, PER_PAGE)
        total_count = j['total_count']
        items = j['artifacts']

        return render_template(
            'list.html',
            items=items,
            page=page,
            per_page=PER_PAGE,
            total_count=total_count
        )


@app.route('/build/<build_id>')
def request_build(build_id):
    r = requests.get(
#        f'{API_URL}/{build_id}/zip',
#        headers={'Authorization': f'token {TOKEN}'},
        '{API_URL}/{build_id}/zip'.format(API_URL=API_URL, build_id=build_id),
        headers={
#            'Authorization': f'token {TOKEN}'
            'Authorization': 'token ' + TOKEN,
            'Accept': 'application/vnd.github.v3+json'
        },
        allow_redirects=False
    )
    return redirect(r.headers['Location'])


if __name__ == '__main__':
    app.run()
