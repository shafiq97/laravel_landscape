<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">


    <link rel="apple-touch-icon" type="image/png"
        href="https://cpwebassets.codepen.io/assets/favicon/apple-touch-icon-5ae1a0698dcc2402e9712f7d01ed509a57814f994c660df9f7a952f3060705ee.png" />

    <meta name="apple-mobile-web-app-title" content="CodePen">

    <link rel="shortcut icon" type="image/x-icon"
        href="https://cpwebassets.codepen.io/assets/favicon/favicon-aec34940fbc1a6e787974dcd360f2c6b63348d4b1f4e06c77743096d55480f33.ico" />

    <link rel="mask-icon" type="image/x-icon"
        href="https://cpwebassets.codepen.io/assets/favicon/logo-pin-b4b4269c16397ad2f0f7a01bcdf513a1994f4c94b8af2f191c09eb0d601762b1.svg"
        color="#111" />




    <title>welcome</title>


    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.9.2/jquery.fullPage.css'>

    <style>
        #next-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            padding: 10px 20px;
        }

        #next-button a {
            display: block;
            padding: 10px 20px;
            background-color: #f15a24;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.5);
            transition: background-color 0.3s ease;
            font-size: 2.8em;
        }

        #next-button a:hover {
            background-color: #d6400f;
        }

        .centered {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .content-wrapper {
            width: 100%;
            height: 100%;
            color: #fff;
            font-family: Montserrat;
            text-transform: uppercase;
            will-change: transform;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transition: all 1.7s cubic-bezier(0.22, 0.44, 0, 1);
            transition: all 1.7s cubic-bezier(0.22, 0.44, 0, 1);
        }

        #landing .content-wrapper {
            color: #333;
        }

        .content-title {
            font-size: 4vh;
            line-height: 1.4;
        }

        p {
            font-size: 2vh;
        }

        #title {
            font-size: 4vh;
            color: #666;
            margin: 20px 0 0 0;
        }

        .section {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            animation: 1s linear;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .section:nth-child(1) {
            z-index: 10;
        }

        .section:nth-child(2) {
            background-image: url(https://images.unsplash.com/photo-1482263231623-6121096b0d3f?dpr=1&auto=compress,format&fit=crop&w=1199&h=799&q=80&cs=tinysrgb&crop=);
            z-index: 9;
        }

        .section:nth-child(3) {
            background-image: url(https://images.unsplash.com/photo-1440549770084-4b381ce9d988?dpr=1&auto=compress,format&fit=crop&w=1199&h=800&q=80&cs=tinysrgb&crop=);
            z-index: 8;
        }

        .section:nth-child(4) {
            z-index: 7;
        }

        .section.fp-completely.active {
            z-index: 20;
        }

        .section.active .content-wrapper {
            transform: translateY(-20vh);
            transition: all 5s cubic-bezier(0.22, 0.44, 0, 1) !important;
        }

        .section.fp-completely .content-wrapper {
            transform: translateY(20vh);
            transition: all 5s cubic-bezier(0.22, 0.44, 0, 1) !important;
        }

        .section.fp-completely.active .content-wrapper {
            margin-top: 0;
            transform: translateY(0);
            position: relative;
        }

        .section.prev.down {
            animation-name: toup;
        }

        .section.active.up {
            animation-name: fromup;
        }

        .section.active.down {
            animation-name: fromdown;
            z-index: 12;
        }

        .section.next.up {
            animation-name: todown;
            z-index: 12;
        }

        @keyframes fromdown {
            from {
                transform: translateY(50%);
            }

            100% {
                transform: translateY(0%);
            }
        }

        @keyframes toup {
            from {
                z-index: 20;
                transform: translateY(0%);
            }

            100% {
                z-index: 20;
                transform: translateY(-100%);
            }
        }

        @keyframes fromup {
            from {
                z-index: 20;
                transform: translateY(-100%);
            }

            100% {
                z-index: 20;
                transform: translateY(0%);
            }
        }

        @keyframes todown {
            from {
                transform: translateY(0%);
            }

            100% {
                transform: translateY(50%);
            }
        }
    </style>

    <script>
        window.console = window.console || function(t) {};
    </script>



</head>

<body translate="no">
    <div id="homepage">
        <section id="landing" class="background section">
            <div class="content-wrapper bgpaper centered">
                <div class="logo">
                    <img src="{{ asset('storage/utils/mountain.avif') }}" >                    <h1 id="title">Lanscaper 4 You</h1>
                </div>
            </div>
        </section>
        <section class="background section" id="about">
            <div class="content-wrapper centered">
                <div class="container">
                    <h3 class="content-title">Welcome!</h3>
                    <p class="content-subtitle">
                        Welcome to our landscape service! We are a team of experienced professionals who are dedicated
                        to providing top-quality landscaping services for both residential and commercial properties.
                    </p>
                </div>
            </div>
        </section>
        <section id="work" class="background section">
            <div class="content-wrapper centered">
                <div class="container">
                    <h3 class="content-title">Our Mission</h3>
                    <p class="content-subtitle">
                        Our mission is to create beautiful and functional outdoor spaces that meet the unique needs and
                        preferences of each of our clients. We take great pride in our work and strive to exceed
                        expectations on every project we take on.</p>
                </div>
            </div>
        </section>
        <section id="contact" class="background section">
            <div class="content-wrapper centered">
                <div class="container">
                    <h3 class="content-title">Why Choosing Us</h3>
                    <p class="content-subtitle">
                        At our landscape service, we believe that communication and collaboration are key to a
                        successful project. That's why we take the time to listen to our clients and work closely with
                        them throughout the design and installation process.</p>
                </div>
            </div>
        </section>
    </div>
    <script
        src="https://cpwebassets.codepen.io/assets/common/stopExecutionOnTimeout-2c7831bb44f98c1391d6a4ffda0e1fd302503391ca806e7fcc7b9b87197aec26.js">
    </script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.9.2/vendors/jquery.easings.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.9.2/vendors/scrolloverflow.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.9.2/jquery.fullPage.min.js'></script>
    <script id="rendered-js">
        $(document).ready(function() {
            $('#homepage').fullpage({
                scrollingSpeed: 1000,
                autoScrolling: true,
                fitToSection: true,
                fitToSectionDelay: 2000,
                anchors: ['home', 'about-us', 'what-we-do', 'our-work'],
                sectionsColor: ['#f2f2f2', '#1BBC9B', '#7E8F7C', '#C63D0F'],
                verticalCentered: false,
                navigation: true,
                navigationPosition: 'right',
                navigationTooltips: ['Eureka Mediatech', 'About Us', 'What we do', 'Our Work'],
                responsiveWidth: 900,
                onLeave: function(index, nextIndex, direction) {
                    if (direction == "up") {
                        $(".section").removeClass("down");
                        $(".section").removeClass("next");
                        $(".section").removeClass("prev");
                        $("#homepage .section:nth-child(" + nextIndex + ")").addClass("up");
                        $("#homepage .section:nth-child(" + nextIndex + ")").next().addClass("next up");
                        $("#homepage .section:nth-child(" + nextIndex + ")").prev().addClass("prev up");
                    } else {
                        $(".section").removeClass("up");
                        $(".section").removeClass("next");
                        $(".section").removeClass("prev");
                        $("#homepage .section:nth-child(" + nextIndex + ")").addClass("down");
                        $("#homepage .section:nth-child(" + nextIndex + ")").next().addClass(
                            "next down");
                        $("#homepage .section:nth-child(" + nextIndex + ")").prev().addClass(
                            "prev down");
                    }
                    console.log(direction + nextIndex);
                }
            });

        });
        //# sourceURL=pen.js
    </script>

    <div id="next-button">
        <a href="{{ route('login') }}" class="btn btn-default">Find your landscaper now!</a>
    </div>
</body>

</html>
