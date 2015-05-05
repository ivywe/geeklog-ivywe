<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>A Single Page FAQ With Toggling Answers Using JQuery</title>
    
    <meta name="description" content="In this lab, we create a single page FAQ with toggling answers using JQuery and some CSS. This is a good solution because it conserves space, allowing users to easily see all the FAQ's first before clicking on one to see the answer. Clicking on one also toggles or slides the answer down into view, instead of moving to another page. Without further ado, let's get into it." />
    <meta name="keywords" content="JQuery, CSS, web, toggle, add class, toggle class">
    
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/base.css" />
    <link rel="stylesheet" href="css/style.css" />
    
    <script src="js/jquery-1.9.1.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#faq-list h2').click(function() {
                
                $(this).next('.answer').slideToggle(500);
                $(this).toggleClass('close');
                
            });
        }); // end ready
    </script>
    

</head>



<body>


<div id="wrapper">


<header>
    <div class="container clearfix">
        <div id="logo">
            <a href="http://callmenick.com"><img src="img/logo.png" /></a>
        </div>
        <div id="title">
            <h1>A Single Page FAQ With Toggling Answers Using JQuery</h1>
        </div>
    </div>
</header>



<div id="main">
    <div class="container">
        
        
        

            
            <h1>Questions For Mr. Carrot</h1>
            <hr />

            <section id="faq-list">
                
                <h2>Is it true that you are a carrot?</h2>
                <div class="answer">
                    <p>
                        Lorem ipsum dolor sit amet, consectetuer
                        adipiscing elit, sed diam nonummy nibh euismod
                        tincidunt ut laoreet dolore magna aliquam erat
                        volutpat. Ut wisi enim ad minim veniam, quis
                        nostrud exerci tation ullamcorper suscipit
                        lobortis nisl ut aliquip ex ea commodo consequat.
                    </p>
                </div>
            
            
                <h2>How does one attain carrot status?</h2>
                <div class="answer">
                    <p>
                        Lorem ipsum dolor sit amet, consectetuer
                        adipiscing elit, sed diam nonummy nibh euismod
                        tincidunt ut laoreet dolore magna aliquam erat
                        volutpat. Ut wisi enim ad minim veniam, quis
                        nostrud exerci tation ullamcorper suscipit
                        lobortis nisl ut aliquip ex ea commodo consequat.
                    </p>
                </div>
            
            
                <h2>Once I attain carrot status, how can I get orange like you?</h2>
                <div class="answer">
                    <p>
                        Lorem ipsum dolor sit amet, consectetuer
                        adipiscing elit, sed diam nonummy nibh euismod
                        tincidunt ut laoreet dolore magna aliquam erat
                        volutpat. Ut wisi enim ad minim veniam, quis
                        nostrud exerci tation ullamcorper suscipit
                        lobortis nisl ut aliquip ex ea commodo consequat.
                    </p>
                </div>
            
            </section>

        
    </div>
</div>


<footer>
    <div class="container">
        <a href="http://www.callmenick.com/labs/single-page-faq">&larr; Back to the article</a>
    </div>
</footer>



</div>


</body>
</html>