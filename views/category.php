<?php $view->script('posts', 'blog:app/bundle/posts.js', 'vue') ?>

<h1>
<?= $category->title ?>
</h1>

<?php foreach ($posts as $post) : ?>
<article id="uk-article">
  <div class="post-type-post">

    <?php if ($image = $post->get('image.src')): ?>
        <figure class="overlay-posts" >
            <div class="img-container">
              <img src="<?= $image ?>" alt="<?= $post->get('image.alt') ?>">
            </div>
            <div class="title entry-img-info">
              <span class="byline">
                <img src="storage/language_logos/source_code_logo.png" alt="Source Code">
              </span>
              <span class="entry-posted-on">
                <span class="byline">
                  <i class="uk-icon-calendar"></i>
                  <time datetime="<?=$post->date->format(\DateTime::W3C)?>" v-cloak>{{ "<?=$post->date->format(\DateTime::W3C)?>" | date "longDate" }}</time>
                </span>
                <span class="byline">
                  <i class="uk-icon-eye"></i>
                  <span><?=$post->visitor_count ?></span>
                  <!--<i class="uk-icon-pencil"></i>
                  <span><?= __('%name%', ['%name%' => $post->user->name]) ?></span>
                -->
                </span>
                <?php if ($category->title !=null): ?>
                  <span class="byline">
                    <i class="uk-icon-list"></i>
                    <span><?= __('%category%', ['%category%' => $category->title]) ?></span>
                  </span>
                <?php endif; ?>
                <span class="byline"></span>
              </span>
            </div>
            <figcaption>
              <h3>Read more</h3>
            </figcaption>
            <a href="<?= $view->url('@blog/id', ['id' => $post->id]) ?>"></a>
        </figure>

    <?php endif ?>


    <div class="uk-grid">

        <div class="uk-width-1-1">

            <h1 class="uk-article-title"><hr style="margin:0 0 10px"><a href="<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><?= $post->title ?></a></h1>
            <?php foreach ($post->getTags() as $tag) : ?>
              &nbsp; <i class="uk-icon-tag"></i> <?php echo $tag; ?>
            <?php endforeach; ?>
            <hr style="margin:0 0 10px">

            <div style="display: flex;  flex-direction: column;  min-height: 20vh;"><?= $post->excerpt ?: $post->content ?></div>

            <ul class="uk-subnav uk-subnav-line tm-subnav">

                <?php if (isset($post->readmore) && $post->readmore || $post->excerpt) : ?>
                <li><a class="uk-animation-hover uk-animation-shake post-buttons uk-button-success" href="<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><?= __('Continue reading!') ?> <i class="uk-icon-long-arrow-right"></i></a></li>
                <?php endif ?>

                <?php if ($post->isCommentable() || $post->comment_count) : ?>
                <li><a class="uk-animation-hover uk-animation-shake post-buttons uk-button-success" href="<?= $view->url('@blog/id#comments', ['id' => $post->id]) ?>"><?= _c('{0} No comments|{1} %num% Comment|]1,Inf[ %num% Comments', $post->comment_count, ['%num%' => $post->comment_count]) ?></a></li>
                <?php endif ?>
                <li>
                  <div class="social_link">
                    <a target="_blank" class="social_item mail uk-animation-hover uk-animation-shake" href="mailto:?subject=<?= $post->title ?>&amp;body=http://lemariva.com<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><i class="uk-icon-justify uk-icon-share"></i></a>
                    <a target="_blank" class="social_item facebook uk-animation-hover uk-animation-shake" href="https://www.facebook.com/sharer/sharer.php?u=http://lemariva.com<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><i class="uk-icon-justify uk-icon-facebook"></i></a>
                    <a target="_blank" class="social_item twitter uk-animation-hover uk-animation-shake" href="https://twitter.com/intent/tweet?text=http://lemariva.com<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><i class="uk-icon-justify uk-icon-twitter"></i></a>
                    <a target="_blank" class="social_item google uk-animation-hover uk-animation-shake" href="https://plus.google.com/share?url=http://lemariva.com<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><i class="uk-icon-justify uk-icon-google-plus"></i></a>
                    <a target="_blank" class="social_item linkedin uk-animation-hover uk-animation-shake" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=http://lemariva.com<?= $view->url('@blog/id', ['id' => $post->id]) ?>&amp;summary=http://lemariva.com<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><i class="uk-icon-justify uk-icon-linkedin"></i></a>
                    <a target="_blank" class="social_item pinterest uk-animation-hover uk-animation-shake" href="http://pinterest.com/pin/create/button/?url=http://lemariva.com<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><i class="uk-icon-justify uk-icon-pinterest"></i></a>
                  </div>
                </li>

            </ul>

        </div>

    </div>
  </div>
</article>
<?php endforeach ?>


<?php

    $range     = 3;
    $total     = intval($total);
    $page      = intval($page);
    $pageIndex = $page - 1;

?>

<?php if ($total > 1) : ?>
<ul class="uk-pagination">


    <?php for($i=1;$i<=$total;$i++): ?>
        <?php if ($i <= ($pageIndex+$range) && $i >= ($pageIndex-$range)): ?>

            <?php if ($i == $page): ?>
            <li class="uk-active"><span><?=$i?></span></li>
            <?php else: ?>
            <li>
                <a href="<?= $view->url('@blog/page', ['page' => $i]) ?>"><?=$i?></a>
            <li>
            <?php endif; ?>

        <?php elseif($i==1): ?>

            <li>
                <a href="<?= $view->url('@blog/page', ['page' => 1]) ?>">1</a>
            </li>
            <li><span>...</span></li>

        <?php elseif($i==$total): ?>

            <li><span>...</span></li>
            <li>
                <a href="<?= $view->url('@blog/page', ['page' => $total]) ?>"><?=$total?></a>
            </li>

        <?php endif; ?>
    <?php endfor; ?>


</ul>
<?php endif ?>
