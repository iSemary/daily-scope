<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        .markdown-content {
            line-height: 1.6;
        }

        .markdown-content h1 {
            color: #11607E;
            border-bottom: 2px solid #11607E;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .markdown-content h2 {
            color: #6c757d;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .markdown-content h3 {
            color: #495057;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .markdown-content code {
            background-color: #f8f9fa;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        .markdown-content pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #11607E;
        }

        .markdown-content blockquote {
            border-left: 4px solid #6c757d;
            padding-left: 15px;
            margin-left: 0;
            color: #6c757d;
        }

        .markdown-content ul,
        .markdown-content ol {
            padding-left: 20px;
        }

        .markdown-content a {
            color: #11607E;
            text-decoration: none;
        }

        .markdown-content a:hover {
            text-decoration: underline;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            padding: 20px;
        }

        .bg-main {
            background-color: #11607E !important;
        }

        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }

        /* Style for anchor links */
        .markdown-content a[href^="#"] {
            color: #11607E;
            text-decoration: none;
            border-bottom: 1px dotted #11607E;
            transition: all 0.3s ease;
        }

        .markdown-content a[href^="#"]:hover {
            color: #0d4a5f;
            border-bottom: 1px solid #0d4a5f;
            text-decoration: none;
        }

        .markdown-content a[href^="#"].active {
            color: #0d4a5f;
            border-bottom: 2px solid #0d4a5f;
            font-weight: bold;
        }

        /* Add some padding to sections for better scroll positioning */
        .markdown-content h2,
        .markdown-content h3,
        .markdown-content h4 {
            scroll-margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-main">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                {{ config('app.name', 'Daily Scope') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="loading" class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div id="error" class="error-message" style="display: none;">
                    <h4>Error Loading Content</h4>
                    <p>Unable to load the README.md file. Please try again later.</p>
                </div>

                <div id="markdown-content" class="markdown-content" style="display: none;">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Daily Scope') }}. Built with Laravel, MySQL, Redis, and Elasticsearch.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Marked.js -->
    <script src="{{ asset('assets/vendor/marked/marked.umd.js') }}"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingElement = document.getElementById('loading');
            const errorElement = document.getElementById('error');
            const contentElement = document.getElementById('markdown-content');

            // Configure marked options
            marked.setOptions({
                breaks: true,
                gfm: true,
                headerIds: false,
                mangle: false
            });

            // Load README.md content
            fetch('/README.md')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(markdownText => {
                    // Parse markdown to HTML
                    const htmlContent = marked.parse(markdownText);

                    // Insert HTML content
                    contentElement.innerHTML = htmlContent;

                    // Hide loading, show content
                    loadingElement.style.display = 'none';
                    contentElement.style.display = 'block';

                    setupAnchorNavigation();
                    setupScrollSpy();
                })
                .catch(error => {
                    console.error('Error loading README.md:', error);

                    loadingElement.style.display = 'none';
                    errorElement.style.display = 'block';
                });

            function setupAnchorNavigation() {
                const headers = document.querySelectorAll('.markdown-content h1, .markdown-content h2, .markdown-content h3, .markdown-content h4');
                headers.forEach(header => {
                    if (!header.id) {
                        const text = header.textContent.toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .replace(/^-|-$/g, '');
                        header.id = text;
                    }
                });

                const anchorLinks = document.querySelectorAll('.markdown-content a[href^="#"]');
                
                anchorLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        const targetId = this.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        
                        if (targetElement) {
                            // Custom smooth scroll function
                            smoothScrollTo(targetElement);
                        }
                    });
                });
            }

            function smoothScrollTo(targetElement) {
                const startPosition = window.pageYOffset;
                const targetPosition = targetElement.offsetTop - 80;
                const distance = targetPosition - startPosition;
                const duration = 300;
                let start = null;

                function animation(currentTime) {
                    if (start === null) start = currentTime;
                    const timeElapsed = currentTime - start;
                    const run = easeInOutQuad(timeElapsed, startPosition, distance, duration);
                    window.scrollTo(0, run);
                    if (timeElapsed < duration) requestAnimationFrame(animation);
                }

                requestAnimationFrame(animation);
            }

            // Easing function for smooth animation
            function easeInOutQuad(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t + b;
                t--;
                return -c / 2 * (t * (t - 2) - 1) + b;
            }

            // Add scroll spy functionality to highlight current section
            function setupScrollSpy() {
                const sections = document.querySelectorAll('.markdown-content h2, .markdown-content h3, .markdown-content h4');
                const navLinks = document.querySelectorAll('.markdown-content a[href^="#"]');

                function updateActiveLink() {
                    let current = '';
                    sections.forEach(section => {
                        const sectionTop = section.offsetTop - 100;
                        if (window.pageYOffset >= sectionTop) {
                            current = section.getAttribute('id');
                        }
                    });

                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + current) {
                            link.classList.add('active');
                        }
                    });
                }

                // Throttled scroll event listener
                let ticking = false;
                function onScroll() {
                    if (!ticking) {
                        requestAnimationFrame(() => {
                            updateActiveLink();
                            ticking = false;
                        });
                        ticking = true;
                    }
                }

                window.addEventListener('scroll', onScroll);
                updateActiveLink(); // Initial call
            }
        });
    </script>
</body>

</html>
