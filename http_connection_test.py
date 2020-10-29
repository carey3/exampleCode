#! /tool/pandora64/bin/python3.6

from http.client import HTTPConnection
from json import dumps
import sys 
import os
import json

sys.path.append('source/libs')
sys.path.append('/tool/gde_csw/usto_infra/prod/site-packages')
sys.path.append('/'.join(os.path.realpath(__file__).split('/')[0:-1]) + "/../libs")

from GFPython import XmlDecoder

headers = {"ACCEPT": "application/json", "Content-type": "application/json", "debug": 2}

# fn is the path and name to the neutral file to be passed to trs
fn = '''/tool/mdp/neutralFileXML/neutralfileXml_FTRF000302'''

print("Reading file: "+fn)

s = open(fn,'r').read()

d = XmlDecoder().decode(s)

body = dumps(d)

client = HTTPConnection(host='alibaba307.fab3.tapeout.cso')

print("Calling trs ")

client.request(method="POST", url='/clynch3/tep_api/trs/recipes',
               body=body,
               headers=headers)
response = client.getresponse()

print(str(response.status))

# just to make things pretty...
json_object = json.loads(response.read())
json_formatted_str = json.dumps(json_object,indent=2)
print(json_formatted_str)
