<?php
/**
 * Arquivo principal do tema PMCell B2B
 * 
 * @package PMCell_B2B
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        
        <?php if (have_posts()) : ?>
            
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="post-meta">
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                                <span class="post-author">por <?php the_author(); ?></span>
                            </div>
                            
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                Leia mais
                            </a>
                        </div>
                        
                    </article>
                    
                <?php endwhile; ?>
            </div>
            
            <?php
            // Paginação
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('Anterior', 'pmcell-b2b'),
                'next_text' => __('Próximo', 'pmcell-b2b'),
            ));
            ?>
            
        <?php else : ?>
            
            <section class="no-results">
                <h2><?php _e('Nenhum conteúdo encontrado', 'pmcell-b2b'); ?></h2>
                <p><?php _e('Parece que não há nada aqui. Que tal fazer uma busca?', 'pmcell-b2b'); ?></p>
                <?php get_search_form(); ?>
            </section>
            
        <?php endif; ?>
        
    </div>
</main>

<?php
get_sidebar();
get_footer();
?>