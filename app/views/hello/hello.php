<h1>Hello World!</h1>

<p>The slug for this page is <?= $this->page ?>!</p>

<p>This text came from the "MyApp" sample application: <strong><?= $this->ello ?></strong></p>

<h3>Posts from the Post model</h3>
<ol>
    <?php foreach ($this->posts as $post) : ?>
    <li><?= $post->post_title ?></li>
    <?php endforeach ?>
</ol>