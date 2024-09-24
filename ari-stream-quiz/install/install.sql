CREATE TABLE IF NOT EXISTS `#__asq_answers` (
  `answer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `answer_guid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` bigint(20) unsigned NOT NULL,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `image_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `answer_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer_correct` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `answer_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`answer_id`),
  KEY `answer_quiz_id` (`quiz_id`),
  KEY `answer_image_id` (`image_id`),
  KEY `answer_question_id` (`question_id`)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__asq_questions` (
  `question_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `question_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `question_order` smallint(11) unsigned NOT NULL DEFAULT '0',
  `question_explanation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `show_explanation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`),
  KEY `question_quiz_id` (`quiz_id`),
  KEY `question_image_id` (`image_id`)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__asq_quizzes` (
  `quiz_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quiz_title_filtered` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quiz_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quiz_image_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `quiz_type` enum('PERSONALITY','TRIVIA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TRIVIA',
  `question_count` smallint(11) unsigned NOT NULL DEFAULT '0',
  `shuffle_answers` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `random_questions` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `random_question_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `use_paging` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `questions_per_page` smallint(6) unsigned NOT NULL DEFAULT '0',
  `start_immediately` tinyint(1) unsigned NOT NULL,
  `theme` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quiz_meta` longtext COLLATE utf8mb4_unicode_ci,
  `author_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `collect_data` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `collect_data_optional` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `collect_email` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `collect_name` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`quiz_id`),
  KEY `quiz_author_id` (`author_id`),
  KEY `quiz_image_id` (`quiz_image_id`),
  KEY `quiz_type` (`quiz_type`),
  KEY `post_id` (`post_id`)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__asq_result_templates` (
  `template_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) NOT NULL,
  `template_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `template_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_point` smallint(11) NOT NULL,
  PRIMARY KEY (`template_id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `image_id` (`image_id`)
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;