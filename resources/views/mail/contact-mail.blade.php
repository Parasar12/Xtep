
<!DOCTYPE html>
<html lang="en">
​
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 
    <style>
        body {
            background-color: #EDF2F7;
            color: #718096;
            margin: 0;
            padding: 0;
        }
        
        .mail {
            margin: 0;
            padding: 0;
            background-color: #EDF2F7;
            padding: 100px 100px;
            height: 100%;
            width: auto;
        }
        
        .mail>h1 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
        }
        
        .mail>p {
            text-align: center;
        }
        
        .container {
            background-color: white;
            padding: 20px 20px;
        }
        
        h1 {
            font-size: 18px;
            line-height: 1.3;
        }
        
        p {
            font-size: 16px;
            letter-spacing: 1px;
            line-height: 1.3;
        }
        
        @media only screen and (max-width:1000px) {
            .mail {
                padding: 60px 60px;
            }
        }
        
        @media only screen and (max-width:767px) {
            .mail {
                padding: 30px 30px;
            }
            h1 {
                font-size: 16px;
            }
            p {
                font-size: 14px;
            }
        }
        
        @media only screen and (max-width:360px) {
            .mail {
                padding: 20px 20px;
            }
            h1 {
                font-size: 12px;
            }
            p {
                font-size: 10px;
            }
        }
    </style>
</head>
​
<body>
    <div class="mail">
        <div class="container">
            <h1>Hello , {{ $user }}</h1>
            <p>We received your messages , we will response as soon as possible</p>
            <p>Thanks,</p>
         
        </div>
        <p>©2022 All rights reserved</p>
    </div>
</body>
​
</html>