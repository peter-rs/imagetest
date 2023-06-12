import mysql.connector 
from config import *
def init_cnx():
    return mysql.connector.connect(user=mysql_user,port=mysql_port, password=mysql_password,host=mysql_host,database=mysql_database)

'''
Schema of table 'nfts'
nft_name = varchar(255) [0]
contract_address = varchar(255) [1]
image_uri = varchar(2000) [2]
nft_key = varchar(255) [3]
moderation_result = varchar(255) [4]
votes_nsfw = int [5]
votes_sfw = int [6]
'''
