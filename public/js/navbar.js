(function () {
    var hamburger = {
        navToggle: document.querySelector('.nav-toggle'),
        nav: document.querySelector('nav'),
        doToggle: function (e) {
            this.navToggle.classList.toggle('js-expanded');
            this.nav.classList.toggle('js-expanded');
        }
    };
    hamburger.navToggle.addEventListener('click', function (e) { hamburger.doToggle(e); });
    hamburger.nav.addEventListener('click', function (e) { hamburger.doToggle(e); });
}());
