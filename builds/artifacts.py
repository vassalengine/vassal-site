#!/bin/python3 

import traceback

from flask import Flask, app, redirect, request, render_template

import requests


app = Flask(__name__)
app.config.from_object('config.Config')

per_page = app.config['PER_PAGE']
token = app.config['TOKEN']
user = app.config['USER']
repo = app.config['REPO']
#api_url = f'https://api.github.com/repos/{user}/{repo}/actions/artifacts'
api_url = 'https://api.github.com/repos/{user}/{repo}/actions/artifacts'.format(user=user, repo=repo)


@app.errorhandler(Exception)
def handle_error(err):
    tb = ''.join(traceback.format_exception(etype=type(err), value=err, tb=err.__traceback__))
    return render_template('error.html', err=tb), 500


@app.route('/')
@app.route('/list')
def show_builds():
    page = int(request.args.get('page', 1))

    r = requests.get(
        api_url,
        params={'per_page': per_page, 'page': page},
#        headers={'Authorization': f'token {token}'}
        headers={'Authorization': 'token ' + token}
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
#        f'{api_url}/{build_id}/zip',
#        headers={'Authorization': f'token {token}'},
        '{api_url}/{build_id}/zip'.format(api_url=api_url, build_id=build_id),
        headers={'Authorization': 'token ' + token},
        allow_redirects=False
    )
    return redirect(r.headers['Location'])


if __name__ == '__main__':
    app.run()
