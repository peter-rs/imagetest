# This file is not used by the container, but instead can be run in a developer environment in order to add unmoderated NFTs to the database.
# The NFTs are then moderated through the classifier.py file.
# There are infinite ways to get NFTs into the database, but this is one example that utilises TezTok's GraphQL API. This example will always get fresh data.
# In order to use a different method, simply call the insert_nft function with your collected data.
import requests
from sql import init_cnx
def insert_nft(nft_name, contract_address, image_uri, nft_key):
    nft_name = nft_name.replace("'", "").replace('"', '').replace('\\', '').strip()
    query = "INSERT INTO nfts (nft_name, contract_address, image_uri, nft_key) VALUES ('{}', '{}', '{}', '{}')".format(nft_name, contract_address, image_uri, nft_key)
    cnx = init_cnx()
    cursor = cnx.cursor()
    cursor.execute(query)
    cursor.commit()
    cnx.close()
query = '''
query LatestEvents {
  events(limit: 1000, order_by: {}, where: {token: {metadata_status: {_eq: "processed"}}}, distinct_on: fa2_address, offset: 2000) {
    token {
      fa2_address
      token_id
      metadata {
        tokens {
          artifact_uri
        }
      }
      name
    }
  }
}
'''
url = "https://api.teztok.com/v1/graphql"
r = requests.post(url, json={'query': query})
print(r.status_code)
data = r.json()['data']
for nft in data['events']:
    nft_name = nft['token']['name']
    image_uri = nft['token']['metadata']['tokens'][0]['artifact_uri']
    nft_key = nft['token']['token_id']
    contract_address = nft['token']['fa2_address']
    try:
        insert_nft(nft_name, contract_address, image_uri, nft_key)
        print("Processed: " + nft_name + " " + contract_address + " " + image_uri + " " + nft_key)
    except Exception as e:
        print("Failed to insert: " + nft_name + " " + contract_address + " " + image_uri + " " + nft_key)
        print(e)