<!DOCTYPE html>
<html lang="zh-tw">
    <head>
        <title>Message</title>

        <?php require_once dirname(__FILE__) . '/head.php'; ?>

    </head>
    <body>
        <div class="container-fluid">

            <?php
            require_once dirname(__FILE__) . '/Message.php';
            $Message = new Message();
            $Data    = $Message->show();
            ?>

            <form class="row" method="post" action="./Message.php">

                <div class="col-md-8">
                    <input type="hidden" name="method" value="edit">
                    <input type="hidden" name="id" value="<?php echo $Data['id']; ?>"
                           placeholder="姓名">
                    <input class="form-control mt-4" type="text" name="person"
                           value="<?php echo urldecode($Data['message_person']); ?>"/>
                    <textarea class="form-control mt-4" name="content" placeholder="留言"
                              style="height: 30vh"><?php echo urldecode($Data['message_content']); ?></textarea>

                    <button class="btn btn-success mt-4">
                        送出
                    </button>
                </div>

            </form>

        </div>

        <?php require_once dirname(__FILE__) . '/footer.php'; ?>

    </body>
</html>
