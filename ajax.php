<?php

use Profspo\Sdk\Client;
use Profspo\Sdk\collections\BooksCollection;
use Profspo\Sdk\collections\JournalsCollection;
use Profspo\Sdk\Managers\IntegrationManager;
use Profspo\Sdk\Models\User;

define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/profspo_catalog/vendor/autoload.php');

require_login();
$action = optional_param('action', "", PARAM_TEXT);
$type = optional_param('type', "", PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);

//book filter
$filter_book_title = optional_param('filter-book-title', "", PARAM_TEXT);

//journal filter
$filter_journal_title = optional_param('filter-journal-title', "", PARAM_TEXT);


$orgId = get_config('profspo_catalog', 'org_id');
$orgToken = get_config('profspo_catalog', 'org_token');
$usrEmail = get_config('profspo_catalog', 'user_email');
$usrPass = get_config('profspo_catalog', 'user_pass');

$content = "";
try {
    $client = new Client($orgId, $orgToken, $usrEmail, $usrPass);
} catch (Exception $e) {
    die();
}

$integrationManager = new IntegrationManager($client);
//$autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT);


switch ($action) {
    case 'getlist':
        switch ($type) {
            case 'book':
                $booksCollection = new BooksCollection($client);

                //set filters
                $booksCollection->setFilter(BooksCollection::TITLE, $filter_book_title);
                $booksCollection->setOffset($booksCollection->getLimit() * $page);
                $booksCollection->get();

                foreach ($booksCollection as $book) {
                    $autoLoginUrl = $integrationManager->generateLoginOrRegisterUrl($USER->email, $USER->email,
                        User::STUDENT, 'books/' . $book->getId());

                    $content .= "<div class=\"profspo-item\" data-id=\"" . $book->getId() . "\">
                                    <div class=\"row\" style='padding: 10px 0'>
                                        <div id=\"profspo-item-image-" . $book->getId() . "\" class=\"col-sm-3\">
                                            <img src=\"" . $book->getCover() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                                            <a id=\"profspo-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . "\"></a>
                                        </div>
                                        <div class=\"col-sm-8\">
                                            <div id=\"profspo-item-title-" . $book->getId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                                            <div id=\"profspo-item-title_additional-" . $book->getId() . "\" hidden><strong>Альтернативное название:</strong> " . $book->getTitleAdditional() . " </div>
                                            <div id=\"profspo-item-pubhouse-" . $book->getId() . "\"><strong>Издательство:</strong> " . $book->getPublishers() . " </div>
                                            <div id=\"profspo-item-authors-" . $book->getId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                                            <div id=\"profspo-item-pubyear-" . $book->getId() . "\"><strong>Год издания:</strong> " . $book->getYear() . " </div>
                                            <div id=\"profspo-item-description-" . $book->getId() . "\" hidden><strong>Описание:</strong> " . $book->getDescription() . " </div>
                                            <div id=\"profspo-item-pubtype-" . $book->getId() . "\" hidden><strong>Тип издания:</strong> " . $book->getType() . " </div>
                                            <div id=\"profspo-item-isbn-" . $book->getId() . "\" hidden><strong>ISBN:</strong> " . $book->getIsbn() . " </div>
                                        </div>
                                    </div>
                                </div>";
                }

                $content .= pagination($booksCollection->getTotal(), $page + 1);
                break;

            case 'journal':
                $journalsCollection = new JournalsCollection($client);

                //set filters
                $journalsCollection->setFilter(JournalsCollection::TITLE, $filter_journal_title);

                $journalsCollection->setOffset($journalsCollection->getLimit() * $page);
                $journalsCollection->get();

                foreach ($journalsCollection as $journal) {
                    $autoLoginUrl = $integrationManager->generateLoginOrRegisterUrl($USER->email, $USER->email,
                        User::STUDENT, 'magazines/' . $journal->getId());
                    $content .= "<div class=\"profspo-item\" data-id=\"" . $journal->getId() . "\">
                                    <div class=\"row\" style='padding: 10px 0'>
                                        <div id=\"profspo-item-image-" . $journal->getId() . "\" class=\"col-sm-3\">
                                            <img src=\"" . $journal->getCover() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                                            <a id=\"profspo-item-url-" . $journal->getId() . "\" href=\"" . $autoLoginUrl . "\"></a>
                                        </div>
                                        <div class=\"col-sm-8\">
                                            <div id=\"profspo-item-title-" . $journal->getId() . "\"><strong>Название:</strong> " . $journal->getTitle() . "</div>
                                            <div id=\"profspo-item-title_additional-" . $journal->getId() . "\" hidden></div>
                                            <div id=\"profspo-item-pubhouse-" . $journal->getId() . "\"><strong>Издательство:</strong> " . $journal->getPublishers() . "</div>
                                            <div id=\"profspo-item-authors-" . $journal->getId() . "\"></div>
                                            <div id=\"profspo-item-pubyear-" . $journal->getId() . "\"></div>
                                            <div id=\"profspo-item-description-" . $journal->getId() . "\" hidden><strong>Описание:</strong> " . $journal->getDescription() . "</div>
                                            <div id=\"profspo-item-isbn-" . $journal->getId() . "\" hidden><strong>ISBN:</strong> " . $journal->getIsbn() . "</div>
                                            <div id=\"profspo-item-pubtype-" . $journal->getId() . "\" hidden></div>
                                        </div>
                                    </div>
                                </div>";
                }

                $content .= pagination($journalsCollection->getTotal(), $page + 1);
                break;

            case 'user':
                break;
        }
        break;
}


echo json_encode(['action' => $action, 'type' => $type, 'page' => $page, 'html' => $content]);

function pagination($count, $page)
{
    $output = '';
    $output .= "<nav aria-label=\"Страница\" class=\"pagination pagination-centered justify-content-center\"><ul class=\"mt-1 pagination \">";
    $pages = ceil($count / 10);


    if ($pages > 1) {

        if ($page > 1) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . ($page - 2) . "\" class=\"page-link profspo-page\" ><span>«</span></a></li>";
        }
        if (($page - 3) > 0) {
            $output .= "<li class=\"page-item \"><a data-page=\"0\" class=\"page-link profspo-page\">1</a></li>";
        }
        if (($page - 3) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link profspo-page\">...</span></li>";
        }


        for ($i = ($page - 2); $i <= ($page + 2); $i++) {
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($page == $i)
                $output .= "<li class=\"page - item active\"><a data-page=\"" . ($i - 1) . "\" class=\"page-link profspo-page\" >" . $i . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($i - 1) . "\" class=\"page-link profspo-page\">" . $i . "</a></li>";
        }


        if (($pages - ($page + 2)) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link profspo-page\">...</span></li>";
        }
        if (($pages - ($page + 2)) > 0) {
            if ($page == $pages)
                $output .= "<li class=\"page - item active\"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link profspo-page\" >" . $pages . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link profspo-page\">" . $pages . "</a></li>";
        }
        if ($page < $pages) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . $page . "\" class=\"page-link profspo-page\"><span>»</span></a></li>";
        }

    }

    $output .= "</ul></nav>";
    return $output;
}


die();
