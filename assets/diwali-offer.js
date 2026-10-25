(function () {
  function formatInr(value) {
    return '₹' + Math.round(Number(value) || 0).toLocaleString('en-IN');
  }

  function initCampaign(node) {
    var endsAt = new Date(node.getAttribute('data-ends-at')).getTime();
    if (!Number.isFinite(endsAt)) return;

    function update() {
      var left = Math.max(0, endsAt - Date.now());
      var total = Math.floor(left / 1000);
      var values = {
        '[data-count-days]': Math.floor(total / 86400),
        '[data-count-hours]': Math.floor((total % 86400) / 3600),
        '[data-count-minutes]': Math.floor((total % 3600) / 60),
        '[data-count-seconds]': total % 60
      };
      Object.keys(values).forEach(function (selector) {
        var field = node.querySelector(selector);
        if (field) field.textContent = String(values[selector]).padStart(2, '0');
      });

      if (left <= 0) {
        if (node.hasAttribute('data-reload-on-expiry')) {
          window.location.reload();
          return false;
        }
        document.querySelectorAll('[data-diwali-campaign]').forEach(function (banner) { banner.remove(); });
        document.querySelectorAll('[data-diwali-price]').forEach(function (price) {
          price.textContent = formatInr(price.getAttribute('data-regular'));
        });
        document.querySelectorAll('[data-diwali-original]').forEach(function (original) { original.remove(); });
        document.querySelectorAll('[data-diwali-badge]').forEach(function (badge) { badge.remove(); });
        document.querySelectorAll('[data-diwali-card]').forEach(function (card) {
          card.setAttribute('data-price', card.getAttribute('data-regular-price'));
        });
        return false;
      }
      return true;
    }

    if (update()) window.setInterval(update, 1000);
  }

  document.querySelectorAll('[data-diwali-campaign]').forEach(initCampaign);
})();
