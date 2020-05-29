<nav class="main-menu">
    <?php
    if ( has_nav_menu( 'main-menu' ) ) {
        wp_nav_menu([
            'theme_location' => 'main-menu',
            'menu_class' => 'menu',
            'walker' => new Walker_Nav_Menu_Wrapped,
            'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
        ]);
    } else {
        wp_nav_menu([
            'theme_location' => 'main-menu',
            'menu_class' => 'menu',
            'walker' => new Walker_Page_Wrapped,
            'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
        ]);
    }
    ?>
</nav>

<script>
(function () {
    window.addEventListener("DOMContentLoaded", function () {
        const nav = document.querySelector("nav.main-menu");
        const toggle = document.querySelector("header .main-menu-toggle")

        // Toggle menu class with menu-toggle
        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            document.body.classList.toggle("main-menu-open")
        })

        // Enable tab navigation for sub-menus
        nav.querySelectorAll("ul li a").forEach(link => {
            link.addEventListener("focus", (e) => {
                // Add `.focus` to all parent li elements
                let element = link;
                while (!element.classList.contains("menu")) {
                    if (element.nodeName === "LI") {
                        element.classList.add("focus")
                    }
                    element = element.parentElement;
                }
            });

            link.addEventListener("blur", (e) => {
                // Clear all focused elements on blur
                nav.querySelectorAll(".focus").forEach(e => e.classList.remove("focus"))
            });
        })
    });

})();
</script>
