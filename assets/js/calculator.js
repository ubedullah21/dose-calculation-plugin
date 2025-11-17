(function($){
    'use strict';

    function roundTo(value, step) {
        if (!step || step === 'none') return value;
        step = parseFloat(step);
        return Math.round(value / step) * step;
    }

    function formatNumber(v, decimals) {
        if (decimals === undefined) {
            return (Math.round(v * 100) / 100).toString();
        }
        return v.toFixed(decimals);
    }

    function initDefaults() {
        var peptides = DC_DEFAULTS.peptides || {};
        var $select = $('#dc-peptide');
        // set first peptide defaults into inputs
        var first = $select.val();
        if (peptides[first]) {
            $('#dc-vial-mg').val(peptides[first].vial_mg);
            $('#dc-total-ml').val(peptides[first].total_ml);
        }
    }

    function applyPeptideDefaults(name) {
        var peptides = DC_DEFAULTS.peptides || {};
        if (peptides[name]) {
            $('#dc-vial-mg').val(peptides[name].vial_mg);
            $('#dc-total-ml').val(peptides[name].total_ml);
        }
        calculateAll();
    }

    function calculateAll() {
        var vialMg = parseFloat($('#dc-vial-mg').val() || 0);
        var totalML = parseFloat($('#dc-total-ml').val() || 0);
        var desired = parseFloat($('#dc-desired-dose').val() || 0);
        var units = $('#dc-dose-units').val();
        var rounding = $('#dc-rounding').val();

        // Normalize desired dose to mg if mcg selected
        var desiredMg = desired;
        if (units === 'mcg') {
            desiredMg = desired / 1000.0;
        }

        // Concentration mg/mL
        var concentration = 0;
        if (totalML > 0) {
            concentration = vialMg / totalML;
        }

        // Dose volume (mL) = desired mg / concentration (mg/mL)
        var doseMl = 0;
        if (concentration > 0) {
            doseMl = desiredMg / concentration;
        }

        // Insulin units (U-100): 1 mL = 100 units
        var unitsValue = doseMl * 100;

        // Rounding for display
        var roundedMl = doseMl;
        if (rounding && rounding !== 'none') {
            roundedMl = roundTo(doseMl, rounding);
        }

        var roundedUnits = Math.round(roundedMl * 100);

        // Update DOM values (formatting)
        $('#dc-concentration').text(formatNumber(concentration, 3) + ' mg/mL');
        $('#dc-dose-volume').text(formatNumber(doseMl, 3) + ' mL');
        $('#dc-dose-rounded').text('Rounded: ' + formatNumber(roundedMl, 3) + ' mL');
        $('#dc-units').text(Math.round(unitsValue).toString() + ' units');
        $('#dc-units-rounded').text('Rounded: ' + roundedUnits + ' units');
    }

    $(document).ready(function(){
        initDefaults();
        calculateAll();

        // events
        $('#dc-peptide').on('change', function(){
            applyPeptideDefaults($(this).val());
        });

        $('#dc-vial-mg, #dc-total-ml, #dc-desired-dose, #dc-dose-units, #dc-rounding').on('input change', function(){
            calculateAll();
        });

        $('#dc-copy').on('click', function(){
            var text = [
                'Concentration: ' + $('#dc-concentration').text(),
                'Dose Volume: ' + $('#dc-dose-volume').text(),
                $('#dc-dose-rounded').text(),
                'Insulin Syringe: ' + $('#dc-units').text(),
                $('#dc-units-rounded').text(),
            ].join('\n');
            navigator.clipboard && navigator.clipboard.writeText ? navigator.clipboard.writeText(text) : alert('Copy not supported in this browser.');
            $(this).text('Copied');
            var self = this;
            setTimeout(function(){ $(self).text('Copy result'); }, 1500);
        });

        $('#dc-reset').on('click', function(){
            // reset to peptide defaults
            applyPeptideDefaults($('#dc-peptide').val());
            $('#dc-desired-dose').val(5);
            $('#dc-dose-units').val('mg');
            $('#dc-rounding').val('0.01');
            calculateAll();
        });
    });
})(jQuery);
