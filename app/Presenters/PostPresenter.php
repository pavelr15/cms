<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
    private $database;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function renderDefault(): void
    {
//        $this->template->posts = $this->database->table('posts');
        $this->template->posts = $this->database->fetchAll('SELECT * FROM posts');
    }

    public function renderShow(int $postId): void
    {
//        $post = $this->database->table('posts')->get($postId);
        $post = $this->database->fetch('SELECT * FROM posts WHERE id = ?', $postId);
        if (!$post) {
            $this->error('Post not found.');
        }

        $this->template->post = $post;
    }

    public function renderEdit(int $postId): void
    {
//        $post = $this->database->table('posts')->get($postId);
        $post = $this->database->fetch('SELECT * FROM posts WHERE id = ?', $postId);
        if (!$post) {
            $this->error('Post not found.');
        }

        $this->template->post = $post;
    }

    protected function createComponentCreatePost(): Form
    {
        $form = new Form;
        $form->addText('title', 'Title:')->setRequired('Please enter post title.');
        $form->addText('subtitle', 'subtitle:');
        $form->addTextArea('content', 'Content:')->setRequired('Please enter post content.');
        $form->addSubmit('send', 'Create');
        $form->onSuccess[] = [$this, 'createPost'];
        return $form;
    }

    public function createPost(Form $form, $data): void
    {
        $postId = $this->getParameter('postId');

        if ($postId) {
//            $post = $this->database->table('posts')->get($postId);
            $post = $this->database->fetch('SELECT * FROM posts WHERE id = ?', $postId);
//            $post->update($data);
            $this->database->query('UPDATE posts SET', $data);
        } else {
            $post = $this->database->table('posts')->insert($data);

            //nejsem si jistý, jak najít post pomocí sql podle id; řádek výše jede v pořádku
//            $this->database->query('INSERT INTO posts ?', $data);
//            $post = $this->database->fetch('SELECT * FROM posts WHERE title = ?', $data['title']);
        }
        $this->flashMessage('Post was successfully created.');
        $this->redirect('Post:show', $post->id);
    }

    public function actionEdit(int $postId): void
    {
//        $post = $this->database->table('posts')->get($postId);
        $post = $this->database->fetch('SELECT * FROM posts WHERE id = ?', $postId);

        if (!$post) {
            $this->error('Post not found.');
        }
//        $this['createPost']->setDefaults($post->toArray());
        $this['createPost']->setDefaults($post);
    }

    public function actionDelete(int $postId): void
    {
//        $post = $this->database->table('posts')->get($postId);
        $post = $this->database->fetch('SELECT * FROM posts WHERE id = ?', $postId);

        if (!$post) {
            $this->error('Post not found.');
        } else {
//            $this->database->table('posts')->get($postId)->delete();
            $this->database->query('DELETE FROM posts WHERE id = ?', $postId);
        }

        $this->flashMessage('Post was successfully deleted.');
        $this->redirect('Post:');
    }
}
