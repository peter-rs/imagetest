from sql import init_cnx
from config import admin_password
import sys
import pandas as pd

def admin_stats():
    cnx = init_cnx()
    cursor = cnx.cursor()
    cursor.execute("SELECT COUNT(*) FROM nfts;")
    total_nfts_in_database = cursor.fetchone()[0]
    cursor.execute("SELECT SUM(votes_nsfw + votes_sfw) FROM nfts;")
    total_votes = cursor.fetchone()[0]
    cursor.execute("SELECT COUNT(*) FROM nfts WHERE moderation_result='blocked';")
    total_nsfw_nfts = cursor.fetchone()[0]
    cursor.execute("SELECT COUNT(*) FROM nfts WHERE moderation_result='allowed';")
    total_sfw_nfts = cursor.fetchone()[0]
    cursor.execute("SELECT COUNT(*) FROM nfts WHERE (moderation_result = 'blocked' AND votes_sfw > votes_nsfw) OR (moderation_result = 'allowed' AND votes_nsfw > votes_sfw);")
    total_collisions = cursor.fetchone()[0]
    print(str(total_nfts_in_database)+";"+str(total_votes)+";"+str(total_nsfw_nfts)+";"+str(total_sfw_nfts) + ";" + str(total_collisions))
    cursor.close()
    cnx.close()
    quit()

def admin_collisions():
    cnx = init_cnx()
    cursor = cnx.cursor()
    cursor.execute("SELECT * FROM nfts WHERE (moderation_result = 'blocked' AND votes_sfw > votes_nsfw) OR (moderation_result = 'allowed' AND votes_nsfw > votes_sfw);")
    collisions = cursor.fetchall()
    print(len(collisions))
    cursor.close()
    cnx.close()
    df = pd.DataFrame(collisions, columns=['nft_name','contract_address','image_uri', 'nft_key', 'moderation_result', 'votes_nsfw', 'votes_sfw'])
    df.to_json('collisions.json', orient='records')
    quit()

def admin_download():
    cnx = init_cnx()
    cursor = cnx.cursor()
    cursor.execute("SELECT * FROM nfts;")
    nfts = cursor.fetchall()
    cursor.close()
    cnx.close()
    df = pd.DataFrame(nfts, columns=['nft_name','contract_address','image_uri', 'nft_key', 'moderation_result', 'votes_nsfw', 'votes_sfw'])
    df.to_json('nfts.json', orient='records')
    df.to_csv('nfts.csv', index=False)
    quit()

if (sys.argv[1] == 'admin_stats'):
    admin_stats()
if (sys.argv[1] == 'admin_collisions'):
    admin_collisions()
if (sys.argv[1] == 'admin_download'):
    admin_download()
if (sys.argv[1] == 'admin_password'):
    try:
        if (str(sys.argv[2]) == admin_password):
            print("auth")
        else:
            print("unauth")
    except Exception:
        print("unauth")