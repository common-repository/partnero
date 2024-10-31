<div id="partnero">

    <div class="partnero-top-bar">
        <img src="<?php echo esc_url(Partnero_Util::get_image_url() . 'logo.svg') ?>" alt="Partnero" height="28" />
    </div>

    <div class="center-wrap">

        <div class="py-2">
            <h1>Welcome!</h1>
            <p class="description">Partnero is a comprehensive partnership management tool that allows you to increase customer acquisition and boost revenue by launching a bespoke affiliate program.</p>
            <ul class="description">
                <li>Customize program settings, adjust commission rates, type & period, cookie lifetime, and affiliate links.</li>
                <li>Track program performance, manage partner accounts, set individual commission rates, and provide your partners with a white-labeled partner portal.</li>
                <li>Optimize your work with automated payouts, partner onboarding, and program emails.</li>
            </ul>
        </div>

        <div class="py-2">
            <h1>Let's get started</h1>
        </div>

        <div class="slim-card white-card">
            <form action='' method='POST'>
                <div class="api-key-wrapper">
                    <h4><label for='api-key'>API key</label></h4>
                    <input type='hidden' name='page' value='partnero-admin'></input>
                    <input id='api-key' name='api_key' class='regular-text' type='password' required></input>
                    <input type='submit' name='submit' id='submit' class='btn' value='Connect'>
                </div>
                <p style="color: #c12020; font-weight: bold;"><?php echo esc_html($error); ?></p>
            </form>

            <hr />

            <p class="small-description"  style="padding: 0; margin: 0;">
                Looking for an API key?
                <strong>
                   <a href="https://help.partnero.com/article/50-how-to-find-my-api-key" target="_blank">Click here for instructions <span class="dashicons dashicons-external"></span></a>
               </strong>
            </p>
        </div>

        <div class="py-2">
            <h1>Need help?</h1>
            <div class="grid quick-links-grid">
                <div class="slim-card white-card">
                    <a href="https://help.partnero.com" target="_blank" class="quick-links-flex-box">
                        <div class="grey-icon">
                            <span class="dashicons dashicons-editor-help"></span>
                        </div>
                        <div>
                            <h3 class="title">
                                Knowledge base
                            </h3>
                            <p class="description" style="margin-bottom: 0; margin-top: 0.625rem;">
                                Access the how-to guides and find answers to the most common questions, all in one place.
                            </p>
                        </div>
                    </a>
                </div>
                <div class="slim-card white-card">
                    <a href="https://help.partnero.com/article/5-getting-started" target="_blank" class="quick-links-flex-box">
                        <div class="grey-icon">
                            <span class="dashicons dashicons-text-page"></span>
                        </div>
                        <div>
                            <h3 class="title">
                                Getting started
                            </h3>
                            <p class="description" style="margin-bottom: 0; margin-top: 0.625rem;">
                                Things you should know about your Partnero account, including account setup and billing.
                            </p>
                        </div>
                    </a>
                </div>

            </div>
            <div class="py-2">
                <p class="description">Feel free to contact us at <a href="mailto:hello@partnero.com">hello@partnero.com</a></p>
            </div>
        </div>

    </div>
</div>
