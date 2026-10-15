<?php require APP_PATH . '/views/layouts/header.php'; ?>
<?php require APP_PATH . '/views/layouts/navbar.php'; ?>

<main class="flex-grow-1 landing-page">
    <section class="landing-hero">
        <div class="container landing-container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 landing-copy">
                    <div class="landing-eyebrow">
                        <span class="eyebrow-dot"></span>
                        A calmer way to care for your crops
                    </div>
                    <h1 class="landing-title">
                        Healthier plants<br>
                        <span>start with a leaf.</span>
                    </h1>
                    <p class="landing-description">
                        Get a clear, practical read on your plant’s health. Upload a leaf photo and let AI help you understand what to do next.
                    </p>
                    <div class="landing-actions">
                        <a href="index.php?route=identify" class="landing-button">
                            Check a plant
                            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                        </a>
                        <a href="index.php?route=diseases" class="landing-text-link">
                            Explore the disease library
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="landing-note">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        Simple guidance, made for the field
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="plant-visual" aria-label="Illustration of a healthy leaf being analyzed">
                        <div class="visual-orbit orbit-one"></div>
                        <div class="visual-orbit orbit-two"></div>
                        <div class="visual-kicker"><span></span> PLANT HEALTH, IN FOCUS</div>
                        <div class="visual-leaf">
                            <svg viewBox="0 0 260 300" role="presentation" aria-hidden="true">
                                <path class="leaf-shape" d="M222 25C119 33 47 71 31 149c-11 53 17 104 64 112 61 10 116-50 127-236Z"/>
                                <path class="leaf-vein" d="M42 254c43-67 97-124 171-207M79 199l-3-61m39 21 5-70m-3 129 65-2m-38-36 63-9"/>
                            </svg>
                        </div>
                        <div class="visual-caption">
                            <span class="caption-icon"><i class="fa-solid fa-sparkles" aria-hidden="true"></i></span>
                            <span><strong>A healthier harvest</strong><small>One leaf at a time</small></span>
                        </div>
                        <div class="visual-stamp"><i class="fa-solid fa-leaf" aria-hidden="true"></i> GROW WITH CONFIDENCE</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-steps">
        <div class="container landing-container">
            <div class="steps-heading">
                <div>
                    <span class="section-overline">A LITTLE HELP, RIGHT WHEN YOU NEED IT</span>
                    <h2>From leaf to next step.</h2>
                </div>
                <p>Know what you’re seeing, then make a more informed decision for your crop.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">01</span>
                        <span class="step-icon"><i class="fa-solid fa-camera" aria-hidden="true"></i></span>
                        <h3>Share a photo</h3>
                        <p>Take a clear picture of the leaf you’re concerned about.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">02</span>
                        <span class="step-icon"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
                        <h3>Get an insight</h3>
                        <p>AI checks for familiar signs of common crop diseases.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">03</span>
                        <span class="step-icon"><i class="fa-solid fa-seedling" aria-hidden="true"></i></span>
                        <h3>Choose what’s next</h3>
                        <p>Explore useful care and prevention guidance for your plant.</p>
                    </article>
                </div>
            </div>
            <div class="landing-disclaimer">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                AI results are a helpful starting point, not a substitute for expert advice.
            </div>
        </div>
    </section>
</main>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
