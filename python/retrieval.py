from sql import init_cnx
import sys
'''
sys.argv[1]
= random_nft
  returns info of a random nft according to the SQL query
    format: nft_name;contract_address;image_uri;nft_key;moderation_result;votes_nsfw;votes_sfw
= vote
    votes for a nft
    sys.argv[2] = contract_address
    sys.argv[3] = nft_key
    sys.argv[4] = position (agree/disagree) [as str]
'''
def get_random_nft():
    cnx = init_cnx()
    cursor = cnx.cursor()
    cursor.execute("SELECT * FROM nfts WHERE (votes_nsfw + votes_sfw) = (SELECT MIN(votes_nsfw + votes_sfw) FROM nfts WHERE moderation_result IS NOT NULL) ORDER BY RAND() LIMIT 1;")
    nft = cursor.fetchone()
    cursor.close()
    cnx.close()
    return nft
def vote_for_nft(nft_contract_address, nft_key, position):
    if (position != "nsfw" and position != "sfw"):
        print("Invalid position")
        return
    cnx = init_cnx()
    cursor = cnx.cursor()
    if (position == "nsfw"):
        column = "votes_nsfw"
    elif (position == "sfw"):
        column = "votes_sfw"
    cursor.execute("UPDATE nfts SET " + column + "=" + column + "+1 WHERE contract_address='" + nft_contract_address + "' AND nft_key='" + nft_key + "';")
    cnx.commit()
    cursor.close()
    cnx.close()
if (sys.argv[1] == 'random_nft'):
    nft = get_random_nft()
    contract_address = nft[1]
    ''' Can potentially add contract exploration like this
    if (contract_address.startswith('0x')): # check if address is ethereum before assuming all 0x addresses are ethereum?
        contract_address = "<a href='https://etherscan.io/address/" + contract_address + "' target='_blank'>" + contract_address + "</a>"
    if (contract_address.startswith('KT') or contract_address.startswith('tz'):
        contract_address = "<a href='https://tzkt.io/" + contract_address + "' target='_blank'>" + contract_address + "</a>"
    '''
    image_uri = nft[2]
    if (image_uri.startswith('ipfs://')):
        image_uri = "https://cloudflare-ipfs.com/ipfs/" + image_uri[7:]
    moderation_result = nft[4]
    if (moderation_result.lower() == "blocked"):
        moderation_result = "NSFW"
    elif (moderation_result.lower() == "allowed"):
        moderation_result = "SFW"
    print(nft[0]+';'+contract_address+';'+image_uri+';'+nft[3]+';'+moderation_result+';'+str(nft[5])+';'+str(nft[6]))
    quit()
elif (sys.argv[1] == 'vote'):
    vote_for_nft(sys.argv[2], sys.argv[3], sys.argv[4].lower())
    quit()