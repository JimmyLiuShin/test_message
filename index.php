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
            $count = (isset($_GET['count']) && $_GET['count'] > 0) ? (int)$_GET['count'] : 10;
            $page = (isset($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;
            $data = $message->index($count, $page);
            ?>

            <div class="row">

                <div class="col-md-8">

                    <div class="row">
                        <div class="col-md-12 mt-4">
                            <nav aria-label="navigation">
                                <ul class="pagination">

                                    <?php
                                    for ($i = 1; $i <= $data['limit']['page_max']; $i++) {
                                        ?>

                                        <li class="page-item<?php if ($i == $data['limit']['page_now'])
                                            echo ' active'; ?>">
                                            <a class="page-link"
                                               href="./?page=<?php echo $i; ?>&count=<?= $data['limit']['count'] ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>

                                        <?php
                                    }
                                    ?>

                                </ul>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <?php
                            foreach ($data['list'] as $key => $value) {
                                ?>

                                <div class="card mt-4">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-10">
                                                <h5 class="card-title">
                                                    <textarea class="form-control"
                                                              disabled><?= urldecode($value['message_content']) ?></textarea>
                                                </h5>
                                                <p class="card-text">
                                                    <?= urldecode($value['message_person']) ?>
                                                    &nbsp;／&nbsp;
                                                    <?= $value['message_time'] ?>
                                                </p>
                                            </div>
                                            <div class="col-md-2">
                                                <form method="post" action="./submit.php"
                                                      id="FormDelete_<?= $value['id'] ?>">
                                                    <input type="hidden" name="method" value="delete">
                                                    <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                </form>
                                                <button class="btn btn-danger" style="display: inline-block"
                                                        onclick="deleteMessage('<?= $value['id'] ?>')">
                                                    刪除
                                                </button>
                                                <button class="btn btn-info" style="display: inline-block"
                                                        onclick="javascript:location.href='./edit.php?id=<?= $value['id'] ?>'">
                                                    修改
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <form method="post" action="./submit.php">
                        <input type="hidden" name="method" value="add">
                        <input class="form-control mt-4" type="text" name="person" placeholder="姓名" required>
                        <textarea class="form-control mt-4" name="content" placeholder="留言" required
                                  style="height: 30vh"></textarea>
                        <button class="btn btn-success mt-4">送出</button>
                    </form>
                </div>

            </div>

        </div>

        <?php require_once dirname(__FILE__) . '/footer.php'; ?>

    </body>
</html>
