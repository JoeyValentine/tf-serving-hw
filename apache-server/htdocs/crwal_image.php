<!DOCTYPE html>
<html>
    <head>
        <title>Results</title>
    </head>
    <body>
        <?php
            // You should do something!
            $host_name = "";
            $user_name = "";
            $password = "";
            $db_name = "";
            $port = "";
            $table_name = "";
            
            $conn = new mysqli($host_name, $user_name, $password, $db_name, $port) or die("DB Connection Failed");

            include('simple_html_dom.php');

            if ($_SERVER["REQUEST_METHOD"] == "POST") 
            {
                $url = $_POST['input_url'];
            }
            
            $html = file_get_html($url);
            $result = new stdClass();
            $result->n_image = 0;
            $result->images = [];
            
            foreach($html->find('img') as $element)
            {
                $result->n_image += 1;
                exec("python3 req.py {$element->src}", $out);

                parse_str($out[0]);

                $image = new stdClass();
                $image->n_detected = $n_detected;
                $image->objs = [];

                for ($i = 1; $i <= $n_detected; ++$i)
                {   
                    parse_str($out[$i]);
                    $obj = new stdClass();
                    $obj->name = $name;
                    $obj->url = $element->src;
                    $obj->prob = $prob;
                    $obj->n_row = $n_row;
                    $obj->n_col = $n_col;
                    $obj->y_min_idx = $y_min_idx;
                    $obj->x_min_idx = $x_min_idx;
                    $obj->box_height = $box_height;
                    $obj->box_width = $box_width;
                    array_push($image->objs, $obj);

                    $query_str = "INSERT IGNORE INTO {$table_name} VALUES ('{$name}', '{$element->src}', {$prob}, {$y_min_idx}, {$x_min_idx}, {$box_height}, {$box_width})";
                    
                    $sql_ret = mysqli_query($conn, $query_str) or die("DB Query Failed");
                }

                array_push($result->images, $image);

                unset($out);
            }

            $conn->close();
        ?>

        <p align="center" id="img_list">
        </p>

        <script type="text/javascript">
            var ret = <?php echo json_encode($result); ?>;
            var p_tag = document.getElementById("img_list");

            for (let img of ret.images)
            {
                for (let obj of img.objs)
                {
                    let canvas_node = document.createElement("canvas");
                    let ctx = canvas_node.getContext("2d");
                    let url_text_node = document.createTextNode(`image URL : ${obj.url}`);
                    let class_text_node = document.createTextNode(`class_name : ${obj.name}`);
                    let prob_text_node = document.createTextNode(`prob : ${obj.prob}`);

                    let img_obj = new Image;

                    img_obj.src = obj.url;
                    canvas_node.width = obj.n_col;
                    canvas_node.height = obj.n_row;

                    img_obj.onload = function() 
                    { 
                        ctx.drawImage(img_obj, 0, 0);

                        // Red rectangle
                        ctx.beginPath();
                        ctx.lineWidth = "3";
                        ctx.strokeStyle = "red";
                        ctx.rect(obj.x_min_idx, obj.y_min_idx, obj.box_width, obj.box_height);
                        ctx.stroke();
                    };

                    p_tag.appendChild(url_text_node);

                    let br_node = document.createElement("br");
                    p_tag.appendChild(br_node);
                    
                    p_tag.appendChild(class_text_node);

                    let br_node1 = document.createElement("br");
                    p_tag.appendChild(br_node1);                

                    p_tag.appendChild(prob_text_node);

                    let br_node2 = document.createElement("br");
                    p_tag.appendChild(br_node2);

                    p_tag.appendChild(canvas_node);

                    let br_node3 = document.createElement("br");
                    p_tag.appendChild(br_node3);

                    let br_node4 = document.createElement("br");
                    p_tag.appendChild(br_node4);
                }
            }
        </script>

    </body>
</html>