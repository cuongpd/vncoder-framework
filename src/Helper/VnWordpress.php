<?php

namespace VnCoder\Helper;

class VnWordpress
{

    /*
     * Để sử dụng được class này bạn cần cài đặt plugin https://github.com/WP-API/Basic-Auth và kích hoạt
     * Để bảo mật bạn nên tạo 1 user mới và chỉ cấp quyền đăng bài
     * Sử dụng:
     * $wp = new VnWordpress('https://yourdomain.com/wp-api/', 'username', 'password');
     * $wp->submitPost('Tiêu đề bài viết', 'Nội dung bài viết', 'https://yourdomain.com/image.jpg', 'category1,category2', 'tag1,tag2');
     */

    protected string $wp_json_api, $wp_user, $wp_password;

    function __construct($wp_api, $username, $password){
        $this->wp_json_api = $wp_api;
        $this->wp_user = $username;
        $this->wp_password = $password;
    }

    public function test(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->wp_json_api); // URL to fetch
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verify SSL certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Check the existence of a common name in the SSL peer certificate and also verify that it matches the hostname provided
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
        $response = curl_exec($ch);

        if ($response === false) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            echo $response;
        }

        curl_close($ch);
    }

    public function submitPost($name, $content, $photo, $category = '', $tags = '', $status = 'publish'){
        $postData = [
            'status' => $status, //publish, future, draft, pending, private
            'title' => $name,
            'content' => $content
        ];
        $featured_media = $this->uploadPhoto($photo , $name);
        if($featured_media){
            $postData['featured_media'] = $featured_media;
        }

        if($category){
            $cateID = [];
            foreach (explode(',', $category) as $item){
                $cateID[] = $this->category($item);
            }
            $postData['categories'] = $cateID;
        }
        if($tags){
            $tagsID = [];
            foreach (explode(',', $tags) as $item){
                $tagsID[] = $this->tags($item);
            }
            $postData['tags'] = $tagsID;
        }

        $postDataJson = json_encode($postData);
        return $this->submitData('posts', $postDataJson);
    }

    public function uploadPhoto($imageUrl, $name) {
        $array = @get_headers($imageUrl);
        if ($array !== false) {
            $string = $array[0];
            if(str_contains($string, "200")) {
                $file = file_get_contents($imageUrl);
                return $this->submitData('media', $file, ['name' => $name]);
            }
        }
        return false;
    }

    public function category($name, $description = ''){
        $postData = [
            'slug' => safe_text($name),
            'name' => $name
        ];
        if($description){
            $postData['description'] = $description;
        }
        $postDataJson = json_encode($postData);
        return $this->submitData('categories', $postDataJson);
    }

    public function tags($tags){
        $postData = [
            'name' => $tags,
        ];
        $postDataJson = json_encode($postData);
        return $this->submitData('tags', $postDataJson);
    }

    public function submitData($action, $postData , $metaData = []){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->wp_json_api . '/wp/v2/' . $action);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_USERPWD, $this->wp_user . ':' . $this->wp_password);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );

        if($action == 'posts' || $action == 'categories' || $action == 'tags'){
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            ] );
        }

        if($action == 'media'){
            if(isset($metaData['name']))
                curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                    'Content-Disposition: form-data; filename="'.safe_text($metaData['name']).'.jpg"',
                ] );
        }

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            logData('wp-error', curl_error($ch) );
            return false;
        }
        curl_close($ch);
        $data = json_decode($result, true);
        $postID = 0;
        if(isset($data['id'])){
            $postID = $data['id'];
        }else{
            if(isset($data['code']) && $data['code'] == 'term_exists'){
                $postID = $data['data']['term_id'];
            }
        }
        return $postID;
    }



}
