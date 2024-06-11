<?php
//update_option('newsletter_sendinblue_welcome', 1, false);
?>
<style>
    .tnp-sendinblue-welcome h1 {
        font-size: 60px;
        color: #fff;
        text-align: center;
        margin-bottom: 30px;
        margin-top: 40px;
                line-height: normal;
        letter-spacing: normal;
        font-weight: bold;
    }

    .tnp-sendinblue-welcome h2 {
        font-size: 30px;
        color: #fff;
        text-align: center;
        line-height: normal;
        letter-spacing: normal;
        font-weight: normal;
    }

    .tnp-button {
        text-align: center;
    }

    .tnp-button a {
        padding: 20px;
        color: #fff;
        background-color: #28AE60;
        font-size: 30px;
        display: inline-block;
        width: auto;
        border-radius: 15px; 
        text-decoration: none;
    }

    .tnp-proceed a {
        background-color: #2A80B9;
        font-size: 20px;
    }        

    .tnp-affiliate {
        font-size: 14px;
        color: #999;
        font-style: italic;
        text-align: center;
    }
</style>
<div class="wrap tnp-sendinblue-welcome" id="tnp-wrap">

    <h1>Welcome to Brevo (formerly Sendinblue) Integration</h1>

    <h2>
        To send your newsletters with Brevo, create an account which includes
        free email delivery.
    </h2>

    <p class="tnp-button"><a href="https://get.brevo.com/7y22fro1brqh" target="_blank">Create an account now</a></p>
    <p class="tnp-affiliate">That is an affiliate link</p>

    <h2>
        Once create, proceed to configure the integration
    </h2>

    <p class="tnp-button tnp-proceed"><a href="?page=newsletter_sendinblue_index&welcome=1">Go to settings page</a></p>


</div>
