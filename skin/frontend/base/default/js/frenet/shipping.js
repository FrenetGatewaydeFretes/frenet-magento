/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

var ProductQuote = Class.create();
ProductQuote.prototype = {
    initialize: function (config) {
        this.url = '/frenet/product/quote';
        this.postcode = null;
        this.active = false;
        this.field = $('frenet-postcode-field');
        this.button = $('frenet-postcode-button');
        this.table = $('frenet-result-table');
        this.tableBody = $('frenet-result-table-body');
        this.errorMessage = null;
        this.priceFormat = config.priceFormat;

        this.field.observe('change', this.check.bind(this));
        this.button.observe('click', this.updateRates.bind(this));
        this.disable();
    },
    check: function () {
        if (this.field.value.length === 0) {
            this.disable();
            this.reset();
            return;
        }

        this.enable();
    },
    reset: function () {
        this.table.hide();
        this.tableBody.update('');
    },
    updateRates: function () {
        this.postcode = this.field.value;
        this.reset();

        if (this.postcode) {
            // this.loaderStart();

            new Ajax.Request(this.url, {
                method: 'POST',
                parameters: $('product_addtocart_form').serialize(),
                onSuccess: this.processSuccess.bind(this),
                onFailure: this.processFailure.bind(this),
                onComplete: this.processAlways.bind(this)
            });
        }

        if (!this.postcode) {
        }
    },
    processSuccess: function (result) {
        // console.log("RESULT SUCCESS", result);
        var response = result.responseJSON;

        if (response.error) {
            this.processFailure(result);
            return;
        }

        this.pushRates(response.rates);
    },
    processFailure: function (result) {
        // console.log("RESULT FAILURE", result);
        // this.reset();
        this.errorMessage(result.message);
        // this.error(true);
    },
    processAlways: function (result) {
        console.log("RESULT ALWAYS", result);
        this.table.show();
        // this.loaderStop();
    },
    pushRates: function (rates) {
        var Rates = $A(rates);

        if (Rates.size() <= 0) {
            return;
            // this.visible(true);
            // this.error(false);
            // this.deactivate();
        }

        Rates.each(this.appendRate.bind(this));

        // if (rates.length === 0) {
        //     this.visible(false);
        // }

        // this.displayNoResults(!this.visible());
    },
    appendRate: function (rate, index) {
        var row = new Element('tr');
        this.createColumn(row, rate.service_description);
        this.createColumn(row, rate.delivery_description);
        this.createColumn(row, this.formatPrice(rate.shipping_price));
        this.tableBody.appendChild(row);
    },
    createColumn: function (row, text) {
        row.appendChild(new Element('td').update(text))
    },
    formatDeliveryTime: function (days) {
        return days + Translator.translate(' day(s)')
    },
    formatPrice: function (price) {
        return formatCurrency(price, this.priceFormat);
    },
    disable: function () {
        this.button.addClassName('disabled');
    },
    enable: function () {
        this.button.removeClassName('disabled');
    }
}
