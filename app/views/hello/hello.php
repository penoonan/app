<h1>Hello World!</h1>

<p>The slug for this page is <?= $this->page ?>!</p>

<h3>Posts</h3>
<ol>
    <?php foreach ($this->posts as $post) : ?>
    <li><?= $post->post_title ?></li>
    <?php endforeach ?>
</ol>