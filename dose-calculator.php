<?php
/*
Plugin Name: Dose Calculator Shortcode
Description: Peptide dose → syringe units calculator. Shortcode: [dose_calculator]
Version: 1.0
Author: Ubed
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DC_Shortcode_Plugin {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_shortcode( 'dose_calculator', [ $this, 'render_shortcode' ] );
    }

    public function enqueue_assets() {
        $plugin_url = plugin_dir_url( __FILE__ );
        wp_enqueue_style( 'dc-style', $plugin_url . 'assets/css/style.css', [], '1.0' );
        wp_enqueue_script( 'dc-script', $plugin_url . 'assets/js/calculator.js', [ 'jquery' ], '1.0', true );

        // Localize default peptide list and strings to JS
        $peptides = [
            // name => [ default_vial_mg, default_total_volume_ml ]
            'Semaglutide' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'Tirzepatide' => [ 'vial_mg' => 5, 'total_ml' => 5 ],
            'MOTS-c' => [ 'vial_mg' => 2, 'total_ml' => 50 ],
            'CJC-1295' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'Ipamorelin' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'BPC-157' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'AOD-9604' => [ 'vial_mg' => 5, 'total_ml' => 5 ],
            // Add more as needed
        ];

        wp_localize_script( 'dc-script', 'DC_DEFAULTS', [
            'peptides' => $peptides,
        ] );
    }

    public function render_shortcode( $atts = [] ) {
        ob_start();
        ?>
        <div class="dc-calculator">
            <h2 class="dc-title">PEPTIDE DOSE → SYRINGE UNITS</h2>

            <div class="dc-grid">
                <label>Peptide
                    <select id="dc-peptide">
                        <?php
                        foreach ( array_keys( (array) $this->get_peptides() ) as $p ) {
                            echo '<option value="' . esc_attr( $p ) . '">' . esc_html( $p ) . '</option>';
                        }
                        ?>
                    </select>
                </label>

                <label>Vial Amount (mg)
                    <input type="number" id="dc-vial-mg" step="any" value="2" />
                </label>

                <label>Total Vial Volume (mL)
                    <input type="number" id="dc-total-ml" step="any" value="50" />
                </label>

                <label>Desired Dose
                    <input type="number" id="dc-desired-dose" step="any" value="5" />
                </label>

                <label>Dose Units
                    <select id="dc-dose-units">
                        <option value="mg">mg</option>
                        <option value="mcg">mcg</option>
                    </select>
                </label>

                <label>Rounding (optional)
                    <select id="dc-rounding">
                        <option value="none">None</option>
                        <option value="0.01">mL to 0.01</option>
                        <option value="0.05">mL to 0.05</option>
                        <option value="0.1">mL to 0.1</option>
                    </select>
                </label>
            </div>

            <div class="dc-results">
                <div class="dc-result-block">
                    <div class="dc-label">Concentration</div>
                    <div class="dc-value" id="dc-concentration">—</div>
                    <div class="dc-sub">mg/mL</div>
                </div>

                <div class="dc-result-block">
                    <div class="dc-label">Dose Volume (mL)</div>
                    <div class="dc-value" id="dc-dose-volume">—</div>
                    <div class="dc-sub" id="dc-dose-rounded">Rounded: —</div>
                </div>

                <div class="dc-result-block">
                    <div class="dc-label">Insulin Syringe (U-100)</div>
                    <div class="dc-value" id="dc-units">—</div>
                    <div class="dc-sub" id="dc-units-rounded">Rounded: —</div>
                </div>
            </div>

            <div class="dc-actions">
                <button id="dc-copy" class="dc-btn">Copy result</button>
                <button id="dc-reset" class="dc-btn dc-btn-outline">Reset</button>
            </div>

            <div class="dc-disclaimer">
                <small>
                This calculator is for educational/reference purposes only and does not constitute medical advice. Always confirm dose and preparation with a licensed clinician and pharmacy instructions before administering any medication.
                </small>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_peptides() {
        // Keep in sync with enqueue_assets peptides array
        return [
            'Semaglutide' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'Tirzepatide' => [ 'vial_mg' => 5, 'total_ml' => 5 ],
            'MOTS-c' => [ 'vial_mg' => 2, 'total_ml' => 50 ],
            'CJC-1295' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'Ipamorelin' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'BPC-157' => [ 'vial_mg' => 2, 'total_ml' => 2 ],
            'AOD-9604' => [ 'vial_mg' => 5, 'total_ml' => 5 ],
        ];
    }
}

new DC_Shortcode_Plugin();
