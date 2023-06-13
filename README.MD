# imagetest
This project was designed as a simple way to gather feedback on the results of any NFT AI Image Classifier. 
## Getting started
Prerequisites:
* <a href='https://www.docker.com/'>Docker</a>
* <a href='https://www.python.org/'>Python 3.8</a> <br>

Local Deployment Steps:
1. Clone the repository and ensure you meet the prerequisite requirements.
2. If you do not yet have a mySQL database setup for this project, please setup the database for remote access and create the "nfts" table exactly as listed in <a href="https://github.com/peter6C6F6C/imagetest/blob/main/python/sql.py">sql.py</a>
    1. If this is the case, you will need to first populate the table using <a href="https://github.com/peter6C6F6C/imagetest/blob/main/python/loader.py">loader.py</a>, this file uses the teztok GraphQL API but as long as you can pass valid data to the `insert_nft()` function, you can use any data source you wish.
    2. Next, you will need to then run your populated results through the AI Image Classifier of your choosing using the <a href="https://github.com/peter6C6F6C/imagetest/blob/main/python/classifier.py">classifier.py</a> file. The file by default uses <a href='https://github.com/Cryptonomic/ImageProxy'>Cryptonomic's ImageProxy</a>, but any classifier can be used as long as you follow the instructions in the `test_nft()` function.
3. Fill out the variables in <a href="https://github.com/peter6C6F6C/imagetest/blob/main/python/config.py">config.py</a> with your data.
4. Build and Run the container on localhost:80 with the following command `docker build -t imagetester . && docker run -p 80:80 -t -i imagetester`
## Usage
From the `index.php` page you can either start classifying images by clicking the 'Start' button, or you can visit the Admin Panel by clicking the 'Settings' button:
<br>
The classification page [`image_test.php`] will show (or load from IPFS) an image and ask you to either classify it as safe for work (SFW) or not safe for work(NSFW). After you answer, it will reveal the result that the classifier generated and the vote counts for each category.
<br>
The admin panel [`admin.php`] display some statistics relating to the database that your instance is configured to use. Additionally, there is a print statement with the number of collisions (A 'collision' is defined as being where the majority of public votes disagrees with the AI classifier) and a link to access a JSON file of all the collisions. Finally, there are download buttons to retreive the entire database in either JSON or CSV form.
