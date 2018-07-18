<?php $view->script('post', 'blog:app/bundle/post.js', 'vue') ?>

<article class="uk-article">

    <?php if ($image = $post->get('image.src')): ?>
    <img src="<?= $image ?>" alt="<?= $post->get('image.alt') ?>">
    <?php endif ?>

    <h1 class="uk-article-title"><?= $post->title ?></h1>
    <?php foreach ($post->getTags() as $tag) : ?>
      &nbsp; <i class="uk-icon-tag"></i> <?php echo $tag; ?>
    <?php endforeach; ?>

    <p class="uk-article-meta">
        <?= __('Written by %name% on %date%', ['%name%' => $this->escape($post->user->name), '%date%' => '<time datetime="'.$post->date->format(\DateTime::ATOM).'" v-cloak>{{ "'.$post->date->format(\DateTime::ATOM).'" | date "longDate" }}</time>' ]) ?>
        <i class="uk-icon-list"></i><span><?= __('%category%', ['%category%' => $post->category->title]) ?></span>
    </p>

    <div class="uk-margin"><?= $post->content ?></div>


    <?php if (count($commendposts) > 0): ?>
    <div class="relatedposts">

      <h3>Related posts</h3>
      <div class="uk-flex thumb">
        <?php foreach ($commendposts as $commend) : ?>
            <?php if($commend->id != $post->id) : ?>
            <a href="<?= $view->url('@blog/id', ['id' => $commend->id]) ?>">
            <div class="uk-panel uk-panel-box tbox uk-overlay">
              <div class="timg">
                <?php if ($image = $commend->get('image.src')): ?>
                    <img src="<?= $image ?>" alt="<?= $commend->get('image.alt') ?>">
                <?php endif // image?>
              </div>
              <h6 class="uk-h6 ttext">
                  <?php echo $commend->title ?>
              </h6>
            </div>
            </a>
            <?php endif ?>
        <?php endforeach ?>
      </div>
    </div>
    <?php endif ?>


    <?= $view->render('blog/comments.php') ?>

</article>
