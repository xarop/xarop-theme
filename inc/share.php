<?php
/**
 * Xarop Theme Share Block
 *
 * Output social share buttons for LinkedIn, Facebook, Twitter, WhatsApp, and Telegram.
 * Usage: Call xarop_theme_share_buttons() after the entry-categories block in your template.
 */

function xarop_theme_share_buttons() {
    if (is_singular()) {
        $url   = urlencode(get_permalink());
        $title = urlencode(get_the_title());
        ?>
        <div class="entry-shared">
            <span>Comparteix:</span>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" target="_blank" rel="noopener" title="Comparteix a LinkedIn">
                LinkedIn
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="noopener" title="Comparteix a Facebook">
                Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" rel="noopener" title="Comparteix a Twitter">
                Twitter
            </a>
            <a href="https://wa.me/?text=<?php echo $title; ?>%20<?php echo $url; ?>" target="_blank" rel="noopener" title="Comparteix a WhatsApp">
                WhatsApp
            </a>
            <a href="https://t.me/share/url?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" rel="noopener" title="Comparteix a Telegram">
                Telegram
            </a>
        </div>
        <?php
    }
}
