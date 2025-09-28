#!/usr/bin/python3

import base64
import hashlib
import json

from flask import Flask, app, jsonify, request
from werkzeug.exceptions import HTTPException

import boto3
import botocore.config
import requests


# curl -X POST -F 'version=1.2.3' -F 'email=bob@example.com' -F 'summary=Another test' -F 'description=I started VASSAL and it kicked my dog.' -F 'log=@/home/uckelman/.VASSAL/errorLog-3.5.7' http://127.0.0.1:5000

app = Flask(__name__)
app.config.from_object('config.Config')

GH_TOKEN = app.config['GH_TOKEN']
GH_USER = app.config['GH_USER']
GH_REPO = app.config['GH_REPO']

S3_KEY = app.config['S3_KEY'] 
S3_SECRET = app.config['S3_SECRET']
S3_REGION = app.config['S3_REGION']
S3_ENDPOINT = app.config['S3_ENDPOINT']
S3_BUCKET = app.config['S3_BUCKET']

GH_API_URL = f'https://api.github.com/repos/{GH_USER}/{GH_REPO}/issues'

GH_HEADERS={
    'Authorization': 'token ' + GH_TOKEN,
    'Accept': 'application/vnd.github.v3+json'
}


LOG_URL = 'https://vassalengine.org/files/gh'


@app.errorhandler(HTTPException)
def handle_httpexception(e):
    response = e.get_response()
    response.data = json.dumps({
        'code': e.code,
        'name': e.name,
        'description': e.description
    })
    response.content_type = 'application/json'
    return response


def create_issue(title, body):
    return requests.post(
        GH_API_URL,
        json={
            'title': title,
            'labels': ['ABR'],
            'body': body
        },
        headers=GH_HEADERS
    )


def upload_file(stream, filename, mime_type, bucket_path, md5):
    config = botocore.config.Config(
        request_checksum_calculation='when_required'
    )

    s3 = boto3.client(
        's3',
        region_name=S3_REGION,
        endpoint_url=S3_ENDPOINT,
        aws_access_key_id=S3_KEY,
        aws_secret_access_key=S3_SECRET,
        config=config
    )

    h64 = base64.b64encode(md5).decode('ascii')

    content_disposition = ('attachment' if not mime_type.startswith('text/plain') and not mime_type.startswith('image/') else 'inline') + '; filename="' + filename + '"'

    s3.put_object(
        Bucket=S3_BUCKET,
        ACL='public-read',
        ContentType=mime_type,
        ContentDisposition=content_disposition,
        Key=bucket_path,
        Body=stream,
        ContentMD5=h64
    )


@app.route('/', methods=['POST'])
def handle_request():
    # collect the report data
    version = request.form['version']
    email = request.form['email']
    summary = request.form['summary']
    description = request.form['description']
    log = request.files['log']

    # hash the log
    md5 = hashlib.md5()
    sha1 = hashlib.sha1()
    buf = bytearray(4096)
    while True:
        rlen = log.readinto(buf)
        if rlen == 0:
            break
        mv = memoryview(buf)[:rlen]
        md5.update(mv)
        sha1.update(mv)

    log_md5 = md5.digest()
    log_sha1 = sha1.hexdigest() 
    log_size = log.tell()
    log.seek(0, 0)

    # upload the log to S3
    upload_file(log, log.filename, 'text/plain; charset=utf-8', 'tracker/gh/' + log_sha1, log_md5)

    log_url = LOG_URL + '/' + log_sha1

    # submit the report to GitHub
    body = f'| ABR | |\n|-----|-----|\n| Version | {version} |\n| Reporter | {email} |\n| Error Log | [{log_size} bytes]({log_url}) |\n\n<pre>{description}</pre>'

    r = create_issue(summary, body) 
    if r.status_code != 201:
        return jsonify(r.json()), r.status_code

    return jsonify({'url': r.json()['url']}), 201
 

if __name__ == '__main__':
    app.run()
