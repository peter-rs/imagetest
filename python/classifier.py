# This file is not used by the container, but instead can be run in a developer environment in order to run unmoderated NFTs through a classifier. 
# The NFTs are added to the database [unmoderated] through the loader.py file or alternate methods.
import requests
from sql import init_cnx
def test_nft(image_uri):
    # Here the image can be ran through any classifier that is available.
    # Please make sure to return either "allowed" or "blocked", and return "INVALID" if an error is encountered.
    # The following example [and default implementation] uses https://github.com/Cryptonomic/ImageProxy which can accept both IPFS and HTTP(S) URIs. 
    api_key = open('api.key', 'r').read()
    if (not image_uri.endswith('.png') and not image_uri.endswith('.jpg') and not image_uri.endswith('.bmp') and not image_uri.endswith('tiff') and not image_uri.endswith('gif') and not image_uri.endswith('jpeg') and not image_uri.startswith('ipfs:')):
        # rudimentary image type checking -- Any file can have any extension.
        return "INVALID" 
    url = "https://imgproxy-prod.cryptonomic-infra.tech"
    json = {"jsonrpc" : "1.0.0", "method": "img_proxy_fetch",
            "params": {
                "response_type" : "Json",
                "url" : image_uri,
                "force": False}}
    headers = {'apikey' : api_key, 'Content-Type' : 'application/json'}
    try:
        response = requests.post(url, headers=headers, json=json)
        response = response.json()
        return response["result"]["moderation_status"]
    except Exception as e:
        print(e)
        print(json)
        return "INVALID"

cnx = init_cnx()
cursor = cnx.cursor()
cursor.execute("SELECT * FROM nfts WHERE moderation_result IS NULL;")
nfts = cursor.fetchall()
for nft in nfts:
    moderation_result = test_nft(nft[2])
    if (moderation_result == "INVALID"):
        print("INVALID: {}".format(nft[2]))
        cursor.execute("DELETE FROM nfts WHERE nft_key = %s AND contract_address = %s", (nft[3], nft[1]))
        cnx.commit()
        continue
    print("{}: {} ({})".format(nft[0], moderation_result, nft[2]))
    cursor.execute("UPDATE nfts SET moderation_result = %s WHERE nft_key = %s AND contract_address = %s", (moderation_result, nft[3], nft[1]))
    cnx.commit()