<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="screen-reader-text"><?php esc_html_e('Search:', 'xarop'); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('Search&hellip;', 'placeholder', 'xarop'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Search', 'xarop'); ?>">
        <span class="screen-reader-text"><?php esc_html_e('Search', 'xarop'); ?></span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <circle cx="11" cy="11" r="7"/>
            <line x1="16.5" y1="16.5" x2="22" y2="22"/>
        </svg>
    </button>
</form>
