<div id="partnero">

    <div class="partnero-top-bar">
        <div>
            <img src="<?php echo esc_url(Partnero_Util::get_image_url() . 'logo.svg'); ?>" alt="Partnero" height="28" />
        </div>
        <div>
            <div class="partnero-top-bar-controls">
                <div>
                    <a href="https://app.partnero.com" target="_blank" class="btn">Log in to Partnero</a>
                </div>
                <div>
                    <form action='' method='POST'>
                        <input type='hidden' name='page' value='partnero-admin'></input>
                        <input type='hidden' name='program_action' value='detach-program'></input>
                        <input type='submit' name='submit' class='btn' value='Detach program'>
                    </form>
                </div>
                <?php if( empty( $result ) ) { /* If there is no response from api show error */ ?>
                    <p style="color: #c12020; font-weight: bold;">
                        Woops! Can't fetch program data. Please try again later.
                    </p>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="center-wrap">

        <div class="program-details-flex-box">
            <h1>Program overview</h1>
        </div>

        <!-- Do not show overview without api response -->
        <?php if( !empty( $result ) ) { $program = $result->overview_program_current_settings; ?>
        <div class="my-4 broad-card white-card">

            <div class="program-overview-wrapper">

                <div class="p-6 program-details">
                    <div class="program-details-flex-box">
                        <div class="program-initials"><?php echo esc_html($program->program_initials); ?></div>
                        <div class="program-name-flex-box">
                            <span class="type"><?php echo esc_html($program->program_type_title); ?></span>
                            <span class="name"><?php echo esc_html($program->program_name); ?></span>
                            <a class="link"
                                href="<?php echo esc_url($program->partner_portal_url); ?>"
                                target="_blank"
                                title="<?php echo esc_url($program->partner_portal_url); ?>"
                            >
                                <span class="dashicons dashicons-admin-links"></span>
                                <span class="url-text"><?php echo esc_url($program->partner_portal_url); ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 program-settings">
                    <ul>
                        <li>
                            <span class="setting-name">Commission</span>
                            <span class="setting-value"><?php echo esc_html($program->commission_description); ?></span>
                        </li>
                        <li>
                            <span class="setting-name">Cookie lifetime</span>
                            <span class="setting-value">
                                <?php if( $program->cookie_lifetime == 1 ) {
                                    echo esc_html($program->cookie_lifetime . " day");
                                } else {
                                    echo esc_html($program->cookie_lifetime . " days");
                                }
                                ?>
                            </span>
                        </li>
                        <li>
                            <span class="setting-name">Payout threshold</span>
                            <span class="setting-value">
                                <?php echo esc_html($program->payout_threshold. " " .$program->currency); ?>
                            </span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>

        <div class="grid all-stats-grid">

            <div class="grid general-stats-grid">

                <div class="slim-card white-card">
                    <div class="card-title">Partners</div>
                    <h3 class="card-value">
                        <span><?php echo (int)$result->total_partners; ?></span>
                        <span class="card-progress <?php echo esc_attr(Partnero_Util::get_growth_class( $result->total_partners_growth )); ?>">
                            <span class="dashicons <?php echo esc_attr(Partnero_Util::get_growth_icon( $result->total_partners_growth )); ?>"></span>
                            <span><?php echo (float)$result->total_partners_growth; ?>%</span>
                        </span>
                    </h3>
                </div>

                <div class="slim-card white-card">
                    <div class="card-title">Signups</div>
                    <h3 class="card-value">
                        <span><?php echo (int)$result->total_signups; ?></span>
                        <span class="card-progress <?php echo esc_attr(Partnero_Util::get_growth_class( $result->total_signups_growth )); ?>">
                            <span class="dashicons <?php echo esc_attr(Partnero_Util::get_growth_icon( $result->total_signups_growth )); ?>"></span>
                            <span><?php echo (float)$result->total_signups_growth; ?>%</span>
                        </span>
                    </h3>
                </div>

                <div class="slim-card white-card">
                    <div class="card-title">Paid customers</div>
                    <h3 class="card-value">
                        <span><?php echo (int)$result->total_paid_accounts; ?></span>
                        <span class="card-progress <?php echo esc_attr(Partnero_Util::get_growth_class( $result->total_paid_accounts_growth )); ?>">
                            <span class="dashicons <?php echo esc_attr(Partnero_Util::get_growth_icon( $result->total_paid_accounts_growth )); ?>">
                            </span>
                            <span><?php echo (float)$result->total_paid_accounts_growth; ?>%</span>
                        </span>
                    </h3>
                </div>

                <div class="slim-card white-card">
                    <div class="card-title">Purchases</div>
                    <h3 class="card-value">
                        <span><?php echo (int)$result->total_purchases; ?></span>
                        <span class="card-progress <?php echo esc_attr(Partnero_Util::get_growth_class( $result->total_purchases_growth )); ?>">
                            <span class="dashicons <?php echo esc_attr(Partnero_Util::get_growth_icon( $result->total_purchases_growth )); ?>"></span>
                            <span><?php echo (float)$result->total_purchases_growth; ?>%</span>
                        </span>
                    </h3>
                </div>

            </div>

            <div>
                <div class="teal-card">

                    <div class="p-6">
                        <div class="card-title">Program revenue</div>
                        <h3 class="card-value">
                            <?php foreach( $result->total_revenue as $revenue ) { ?>
                                <span><?php echo esc_html($revenue); ?></span>
                            <?php } ?>
                            <span class="card-progress <?php echo esc_attr(Partnero_Util::get_growth_class( $result->total_revenue_growth )); ?>">
                                <span class="dashicons <?php echo esc_attr(Partnero_Util::get_growth_icon( $result->total_revenue_growth )); ?>">
                                </span>
                                <span><?php echo (float)$result->total_revenue_growth; ?>%</span>
                            </span>
                        </h3>
                    </div>

                    <div class="teal-card-line"></div>

                    <div class="p-6">
                        <div class="card-title">Rewards</div>
                        <h3 class="card-value">
                            <?php foreach( $result->total_reward as $reward ) { ?>
                                <span><?php echo esc_html($reward); ?></span>
                            <?php } ?>
                            <span class="card-progress <?php echo esc_attr(Partnero_Util::get_growth_class( $result->total_reward_growth )); ?>">
                                <span class="dashicons <?php echo esc_attr(Partnero_Util::get_growth_icon( $result->total_reward_growth )); ?>"></span>
                                <span><?php echo (float)$result->total_reward_growth; ?>%</span>
                            </span>
                        </h3>
                        <div>
                            <?php foreach( $result->total_paid as $paid ) { ?>
                                <span class='mini-text'><?php echo esc_html($paid); ?></span>
                            <?php } ?>
                            <span>paid</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="my-4 p-6 broad-card white-card">
            <form action='' method='POST' class="app-settings">
                <div>
                    <label for="tax_setting" class="title">
                        Select which sale amount you’d like to calculate commissions from:
                    </label>
                    <select name="tax_setting">
                        <option
                            value="net"
                            <?php if( $tax_setting === 'net' ) { echo 'selected'; } ?>
                        >
                            NET (after tax is deducted)
                        </option>

                        <option
                            value="gross"
                            <?php if( $tax_setting === 'gross' ) { echo 'selected'; } ?>
                        >
                            GROSS (total amount, tax included)
                        </option>
                    </select>
                </div>
                <div>
                    <input type='hidden' name='page' value='partnero-admin'></input>
                    <input type='hidden' name='program_action' value='update-tax-setting'></input>
                    <input type='submit' name='submit' class='btn' value="Save">
                </div>
            </form>
        </div>
        <?php } ?>

        <div class="py-2">
            <p class="description">Need help? Feel free to contact us at <a href="mailto:hello@partnero.com">hello@partnero.com</a></p>
        </div>

    </div>

</div>
