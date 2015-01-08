<?php
class ModelArticleInformation extends Model {
	public function getTotalArticle() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "article");

		return $query->row['total'];
	}

	public function getArticle($article_id) {

		$query = $this->db->query("SELECT DISTINCT *, (SELECT DISTINCT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'article_id=" . (int)$article_id . "') AS keyword FROM " . DB_PREFIX . "article a LEFT JOIN " . DB_PREFIX . "article_description ad ON (a.article_id = ad.article_id) WHERE a.article_id = '" . (int)$article_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getArticles($data = array()) {
		$sql = "SELECT a.article_id AS article_id, a.*, ad.name FROM " . DB_PREFIX . "article a LEFT JOIN " . DB_PREFIX . "article_description ad ON (a.article_id = ad.article_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY a.article_id";

		$sort_data = array(
			'name',
			'sort_order',
			'date_added',
			'date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function addArticle($data) {		

		$this->db->query("INSERT INTO " . DB_PREFIX . "article SET `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

		$article_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "article SET image = '" . $this->db->escape($data['image']) . "' WHERE article_id = '" . (int)$article_id . "'");
		}

		foreach ($data['article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "article_description SET article_id = '" . (int)$article_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['article_categories'])) {
			foreach ($data['article_categories'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_to_category SET article_id = '" . (int)$article_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['article_store'])) {
			foreach ($data['article_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_to_store SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'article_id=" . (int)$article_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		if (isset($data['article_image'])) {
			foreach ($data['article_image'] as $article_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_image SET article_id = '" . (int)$article_id . "', image = '" . $this->db->escape($article_image['image']) . "', sort_order = '" . (int)$article_image['sort_order'] . "'");
			}
		}

		if (isset($data['article_youtube'])) {
			foreach ($data['article_youtube'] as $article_youtube) {
				if (isset($article_youtube['url']) && (!empty($article_youtube['url']))) {

					$url = html_entity_decode($article_youtube['url']);
					parse_str(parse_url($url, PHP_URL_QUERY ),$youtube_url);			

					if (isset($youtube_url['v'])) {
						$video = $youtube_url['v'];
					} else if (strpos($url, 'v=') !== false) {
						$video = str_replace('v=', '', $url);
					} else {
						$video = $article_youtube['url'];
					}

					$controls = (isset($article_youtube['controls']))? $article_youtube['controls'] : 0;
					$autoplay = (isset($article_youtube['autoplay']))? $article_youtube['autoplay'] : 0;

					$this->db->query("INSERT INTO " . DB_PREFIX . "article_youtube SET article_id = '" . (int)$article_id . "', image = '" . $this->db->escape($article_youtube['image']) . "', url = '" . $this->db->escape($video) . "', `controls` = '" . (int)$controls . "', `auto_play` = '" . (int)$autoplay . "', sort_order = '" . (int)$article_youtube['sort_order'] . "'");
				}
			}
		}		

		if (isset($data['article_related'])) {
			
			foreach ($data['article_related'] as $article_related) {

				$this->db->query("DELETE FROM " . DB_PREFIX . "article_related WHERE article_id = '" . (int)$article_id . "' AND article_related_id = '" . (int)$article_related['article_id'] . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_related SET article_id = '" . (int)$article_id . "', article_related_id = '" . (int)$article_related['article_id'] . "', sort_order = '" . (int)$article_related['sort_order'] . "'");
			}
		}

		$this->cache->delete('article');		

		return $article_id;
	}

	public function editArticle($article_id, $data) {		

		$this->db->query("UPDATE " . DB_PREFIX . "article SET `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "article SET image = '" . $this->db->escape($data['image']) . "' WHERE article_id = '" . (int)$article_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_description WHERE article_id = '" . (int)$article_id . "'");

		foreach ($data['article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "article_description SET article_id = '" . (int)$article_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_to_store WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_store'])) {
			foreach ($data['article_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_to_store SET article_id = '" . (int)$article_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_to_category WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_categories'])) {
			foreach ($data['article_categories'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_to_category SET article_id = '" . (int)$article_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'article_id=" . (int)$article_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'article_id=" . (int)$article_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_image WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_image'])) {
			foreach ($data['article_image'] as $article_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_image SET article_id = '" . (int)$article_id . "', image = '" . $this->db->escape($article_image['image']) . "', sort_order = '" . (int)$article_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_youtube WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_youtube'])) {
			foreach ($data['article_youtube'] as $article_youtube) {
				if (isset($article_youtube['url']) && (!empty($article_youtube['url']))) {

					$url = html_entity_decode($article_youtube['url']);
					parse_str(parse_url($url, PHP_URL_QUERY ),$youtube_url);			

					if (isset($youtube_url['v'])) {
						$video = $youtube_url['v'];
					} else if (strpos($url, 'v=') !== false) {
						$video = str_replace('v=', '', $url);
					} else {
						$video = $article_youtube['url'];
					}

					$controls = (isset($article_youtube['controls']))? $article_youtube['controls'] : 0;
					$autoplay = (isset($article_youtube['autoplay']))? $article_youtube['autoplay'] : 0;

					$this->db->query("INSERT INTO " . DB_PREFIX . "article_youtube SET article_id = '" . (int)$article_id . "', image = '" . $this->db->escape($article_youtube['image']) . "', url = '" . $this->db->escape($video) . "', `controls` = '" . (int)$controls . "', `auto_play` = '" . (int)$autoplay . "', sort_order = '" . (int)$article_youtube['sort_order'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_related WHERE article_id = '" . (int)$article_id . "'");

		if (isset($data['article_related'])) {

			foreach ($data['article_related'] as $article_related) {

				$this->db->query("DELETE FROM " . DB_PREFIX . "article_related WHERE article_id = '" . (int)$article_id . "' AND article_related_id = '" . (int)$article_related['article_id'] . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_related SET article_id = '" . (int)$article_id . "', article_related_id = '" . (int)$article_related['article_id'] . "', sort_order = '" . (int)$article_related['sort_order'] . "'");
			}
		}

		$this->cache->delete('article');
		
	}

	public function getArticleDescriptions($article_id) {
		$article_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_description WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $article_description_data;
	}

	public function getArticleCategories($article_id) {
		$article_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_to_category WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_store_data[] = $result['category_id'];
		}

		return $article_store_data;
	}

	public function getArticleStores($article_id) {
		$article_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_to_store WHERE article_id = '" . (int)$article_id . "'");

		foreach ($query->rows as $result) {
			$article_store_data[] = $result['store_id'];
		}

		return $article_store_data;
	}

	public function getArticleRelated($article_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_related WHERE article_id = '" . (int)$article_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getArticleImages($article_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_image WHERE article_id = '" . (int)$article_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getArticleYoutubes($article_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_youtube WHERE article_id = '" . (int)$article_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function deleteArticle($article_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "article WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "article_description WHERE article_id = '" . (int)$article_id . "'");		
		$this->db->query("DELETE FROM " . DB_PREFIX . "article_to_store WHERE article_id = '" . (int)$article_id . "'");		
		$this->db->query("DELETE FROM " . DB_PREFIX . "article_to_category WHERE article_id = '" . (int)$article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'article_id=" . (int)$article_id . "'");

		$this->cache->delete('article');
		
	}
}
