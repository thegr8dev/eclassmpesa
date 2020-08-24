#Eclass Mpesa Add On Installing and Setup Wizard

#####Before installing this package make sure [smodav/mpesa](https://github.com/SmoDav/mpesa) is installed.

### Requirements

1. Eclass version v2.2 or above.
2. php version 7.2 or 7.3 must be.

### Installation 

Run the package using follwing command: 

`composer require eclass/eclassmpesa`

After successully install add run the following command:

`php artisan eclassmpesa:install`

After run the command add following variables in .env file of your script

Open** .env** file and add this:

- MPESA_DEFAULT=staging

- MPESA_KEY=

- MPESA_SECRET=

- MPESA_INITIATOR=

- MPESA_PASSKEY=

- MPESA_PAYBILL=

- MPESA_SHORTCODE=

- MPESA_ENABLE=0

- MPESA_VALIDATION=

- MPESA_CALLBACK=

#### Note:

Update the following URLs  in your .env  file

Update mpesa validation url in **MPESA_VALIDATION**

Your mpesa validation url will be : 

`https://yourdomain.com/public/api/payment/validation`

Update mpesa callback url in **MPESA_CALLBACK**

Your mpesa callback url will be : 

`https://yourdomain.com/public/api/payment/confirm/callback`

Remove **public**  if your domain not have in url.


**Wohoo ! You Successfully installed the Mpesa Add On your eClass.**

### Update FAQ.

**Q. What if new update came?** 

Well add-on we updated time to time and you should keep eye on the addon page by bookmarking it. if a new update came just hit follwing command. new version will be released with new version tag.

`composer update`

**on your project root directory**.








