<!DOCTYPE html>
<html lang="zh-tw">
    <head>
        <?php
        require_once dirname(__FILE__) . '/autoload.php';
        require_once dirname(__FILE__) . '/head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid">

            <?php

            use Controller\Message;

            $message = new Message();
            $data = $message->show();
            $data || header('Location:./?alert=error_notFind');
            ?>

            <form class="row" method="post" action="./submit.php">

                <div class="col-md-8">
                    <input type="hidden" name="method" value="edit">
                    <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <input class="form-control mt-4" type="text" name="person" placeholder="姓名"
                           value="<?= urldecode($data['message_person']) ?>"/>
                    <textarea class="form-control mt-4" name="content" placeholder="留言"
                              style="height: 30vh"><?= urldecode($data['message_content']) ?></textarea>

                    <button class="btn btn-success mt-4" type="submit">
                        送出
                    </button>
                </div>

            </form>

        </div>

        <?php require_once dirname(__FILE__) . '/footer.php'; ?>

    </body>
</html>
