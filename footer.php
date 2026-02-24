</main>

<footer class="site-footer">
    <div class="footer-inner">

    <a href="https://www.red.es/es/iniciativas/proyectos/kit-digital" target="_blank" rel="noreferrer noopener" style="margin:2rem auto;">
        <img loading="lazy" decoding="async" width="1024" height="119" src="<?php echo get_template_directory_uri(); ?>/assets/img/Logo-digitalizadores.png" alt="Logo digitalizadores">
    </a>


        <div class="site-info">
         
                &copy; <?php echo date('Y'); ?> 
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php bloginfo('name'); ?>
                </a>
                ·
                <?php
                /* translators: %s: WordPress */
                printf(esc_html__('Developed in Barcelona by %s', 'xarop'), '<a href="https://xarop.com/">xarop.com</a>');
                ?>
            
        </div>

        <?php if (has_nav_menu('footer-menu') ) : ?>
            <nav class="footer-navigation" role="navigation" aria-label="<?php esc_attr_e('Footer Menu', 'xarop'); ?>">
            <?php
            wp_nav_menu(
                array(
                'theme_location' => 'footer-menu',
                'menu_id'        => 'footer-menu',
                'container'      => false,
                'depth'          => 1,
                )
            );
            ?>
            </nav>
        <?php endif; ?>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
