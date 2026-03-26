<!doctype html>
<html lang="en">


<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b5f7a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Djibah SeaBid Pro">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('home/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icon CSS -->
    <link href="{{ asset('home/assets/css/bootstrap-icons.css') }}" rel="stylesheet">
  
    <!--Nice Select CSS -->
    <link rel="stylesheet" href="{{ asset('home/assets/css/nice-select.css') }}">

    <link rel="stylesheet" href="{{ asset('home/assets/css/style.css') }}">
    <!-- Title -->
    <title>Djibah SeaBid Pro</title>
    <link rel="icon" href="{{ asset('home/assets/img/logo.png') }}" type="image/png" sizes="20x20">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('home/assets/img/logo.png') }}">
    <style>
        .install-app-button {
            position: absolute;
            right: 24px;
            bottom: 24px;
            z-index: 20;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            border: 0;
            border-radius: 12px;
            background: #16a34a;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        }

        .install-app-button:hover {
            background: #15803d;
            color: #fff;
        }

        .install-app-button.is-visible {
            display: inline-flex;
        }

        .install-app-button.is-hidden {
            display: none !important;
        }

        @media (max-width: 768px) {
            .install-app-button {
                right: 16px;
                bottom: 16px;
                padding: 10px 14px;
                font-size: 14px;
            }
        }

        .hero-section {
            position: relative;
        }
    </style>
</head>

<body id="body">

  
    

    <div class="position-fixed" style="top:20px; right:5%; z-index:9999">
    <select id="languageSwitcher" class="input-field">
        <option value="en">English</option>
        <option value="ar">Arabic</option>
        <option value="fr">French</option>
    </select>
</div>
  <!-- ===================== HERO SECTION (DARK) ===================== -->
<section class="hero-section text-white d-flex align-items-center">
    <div class="container text-center" style="z-index: 99999;">
        <img src="{{ asset('home/assets/img/djibah-logo.png') }}" width="70" class="mb-3">

        <h3 class="fw-bold"><span class="text-info">Djibah </span><br>SeaBid Pro</h3>
        <p class="lead mt-3 text-white small mb-3">
            The marketplace connecting fresh fish sellers and buyers
        </p>

        <div class="row justify-content-center g-4 mb-5 mt-3">
            <div class="col-md-5">
                <div class="card-option glass-card">
                    <div class="icon">🛒</div>
                    <h4>Join as Buyer</h4>
                    <p class="text-white">Participate in auctions and purchase seafood lots</p><a class="btn btn-primary w-100 mt-3" href="{{ route('home.buyer-register') }}">Join as Buyer</a>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card-option glass-card">
                    <div class="icon">🐟</div>
                    <h4>Join as Seller</h4>
                    <p class="text-white">List products and sell through auctions</p><a class="btn btn-success w-100 mt-3" href="{{ route('home.seller-register') }}" style="padding: 0.75rem 1.5rem;">Join as Seller</a>
                </div>
            </div>
        </div>
        <div class="text-center mt-2 row justify-content-center ">
           <div class="col-md-4"><div class="glass-card py-2 text-white"> Already have an account? <a class="text-info" href="{{ route('home.login') }}">Login</a></div>
          
           </div>

        <p class="mt-3 small opacity-50 mb-5">Powered by Djibah Seafood</p>
    </div>
    <button id="installAppButton" type="button" class="install-app-button" style="display:inline-flex;">
        <i class="bi bi-download"></i>
        <span>Install App</span>
    </button>
</section>



    


   
    <script src="{{ asset('home/assets/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Popper and Bootstrap JS -->
    <script src="{{ asset('home/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('home/assets/js/bootstrap.min.js') }}"></script>
 
    <!-- Nice Select  JS -->
    <script src="{{ asset('home/assets/js/jquery.nice-select.min.js') }}"></script>
  
   
    
 

    <script src="{{ asset('home/assets/js/main.js') }}"></script>
    <script>
        let deferredInstallPrompt = null;
        const installAppButton = document.getElementById('installAppButton');
        const languageSwitcher = document.getElementById('languageSwitcher');

        const isRunningStandalone = () =>
            window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        const hideInstallButton = () => {
            installAppButton.classList.add('is-hidden');
            installAppButton.style.display = 'none';
        };

        const showInstallButton = () => {
            if (!isRunningStandalone()) {
                installAppButton.classList.add('is-visible');
                installAppButton.classList.remove('is-hidden');
                installAppButton.style.display = 'inline-flex';
            }
        };

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredInstallPrompt = event;
            showInstallButton();
        });

        installAppButton.addEventListener('click', async () => {
            const wantsToInstall = confirm('Do you want to install Djibah SeaBid Pro?');

            if (!wantsToInstall) {
                return;
            }

            if (!deferredInstallPrompt) {
                alert('Install is not available from this button right now. Please use your browser menu and choose Install App or Add to Home Screen.');
                return;
            }

            deferredInstallPrompt.prompt();
            const choiceResult = await deferredInstallPrompt.userChoice;

            if (choiceResult.outcome === 'accepted') {
                hideInstallButton();
            }

            deferredInstallPrompt = null;
        });

        window.addEventListener('appinstalled', () => {
            deferredInstallPrompt = null;
            hideInstallButton();
        });

        window.addEventListener('load', () => {
            if (isRunningStandalone()) {
                hideInstallButton();
                return;
            }

            // Show a fallback button even when the browser does not fire beforeinstallprompt.
            showInstallButton();
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('service-worker.js') }}');
            });
        }

        if (languageSwitcher) {
            const savedLanguage = localStorage.getItem('siteLanguage');

            if (savedLanguage) {
                languageSwitcher.value = savedLanguage;
                if (window.jQuery && jQuery.fn.niceSelect) {
                    jQuery(languageSwitcher).niceSelect('update');
                }
            }

            languageSwitcher.addEventListener('change', function () {
                const selectedLanguage = this.value;
                localStorage.setItem('siteLanguage', selectedLanguage);

                document.documentElement.lang = selectedLanguage;
                document.body.setAttribute('data-language', selectedLanguage);

                // Placeholder hook for future translation wiring.
                window.location.reload();
            });
        }
    </script>

</body>

</html>
