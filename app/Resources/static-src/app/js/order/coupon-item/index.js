class Coupon {
  constructor(props) {
    this.$element = props.element;
    this.$showDeductAmount = this.$element.find('#show-deduct-amount');
    this.$noUseCouponCode = this.$element.find('#no-use-coupon-code');
    this.$couponCode = this.$element.find("input[name='couponCode']");
    this.$selectCoupon = this.$element.find('#coupon-select');
    this.$couponNotify =  this.$element.find('#code-notify');
    this.initEvent();
    this.init();
  }

  initEvent() {
    const $element = this.$element;
    $element.on('change', '#coupon-select', event => this.couponSelect(event));
    $element.on('click', '#change-coupon-code', event => this.showChangeCoupon(event));
    $element.on('click', '#cancel-coupon', event => this.cancelCoupon(event));
    $element.on('click', '#use-coupon', event => this.useCoupon(event));
  }

  init() {
    if (this.$selectCoupon.length > 0) {
      this._setCoupon(this.$selectCoupon.val(), false);
    }

    this._showDeductAmount();
  }

  couponSelect(event) {
    const $this = $(event.currentTarget);
    const coupon = $this.find('option:selected');
    const val = $this.val();

    if (!val) {
      this._selectEmptyCoupon();
      return;
    }

    this._setCoupon(val);
  }

  showChangeCoupon(event) {
    const $this = $(event.currentTarget);

    this._showCouponCode();
    this._setCoupon().focus();  
  }

  useCoupon() {
    this.$couponCode
    this._setCoupon(this.$couponCode.val());
  }

  _checkCoupon() {
    let self = this;
    let code = this.$couponCode.val();
    if (!this.$productType) {
      this.$productType = $("input[name='targetType']");
    }
    if (!this.$productId) {
      this.$productId = $("input[name='targetId']");
    }
    if (!code) {
        self.$couponNotify.css("display","none");
        return;
    }
    let data = {
      'code': code,
      'targetType':this.$productType.val(),
      'targetId': this.$productId.val(),
      'price': $("input[name='price']").val()
    }

    $.post($('#use-coupon').data('url'), data, function(data){
        if(data.useable == 'no'){
          self.$couponNotify.addClass('alert-danger').text(data.message).css("display","inline-block");
          self._showDeductAmount();
        } else {
          let text = data['type'] == 'discount' ? Translator.trans('order.create.use_discount_coupon_hint', {rate: data['rate']}) : Translator.trans('order.create.use_price_coupon_hint', {rate: data['rate']});
          self.$couponNotify.removeClass('alert-danger').addClass("alert-success").text(text).css("display","inline-block");
          self._showDeductAmount(data.deduct_amount_format);
        }
    })
  }

  cancelCoupon(event) {
    this._hideCouponCode();
    this.$selectCoupon.trigger('change');
  }

  _showDeductAmount(amount = this.$showDeductAmount.data('placeholder')) {
    //显示优惠码优惠的金额
    this.$showDeductAmount.text(amount);
  }

  _showCouponCode() {
    //显示手动输入优惠码框,隐藏select，去除右侧优惠金额信息展示
    $('#coupon-code').show();
    $('#select-coupon-box').hide();
    this._showDeductAmount();
  }

  _hideCouponCode() {
    //隐藏手动输入优惠码框，显示select
    $('#coupon-code').hide();
    $('#select-coupon-box').show();
  }

  _selectEmptyCoupon() {
    this._setCoupon();
    this._showDeductAmount();
  }

  _setCoupon(value = '', triggerCaculate = true) {
    //设置选择的优惠码code
    this.$couponCode.val(value);
    !value ? this.$noUseCouponCode.show() : this.$noUseCouponCode.hide();
    this._checkCoupon();
    if (triggerCaculate) {
     this._calculatePrice();
    }
    return this.$couponCode;
  }

  _calculatePrice() {
    $('#order-create-form').trigger('calculatePrice');
  }
}


new Coupon({
  element: $('#coupon-deducts')
});