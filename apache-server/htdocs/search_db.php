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

            if ($_SERVER["REQUEST_METHOD"] == "POST") 
            {
                $obj_name = $_POST['obj_name'];
                $prob = (float)$_POST['prob'];
            }

            $query_str = "SELECT * FROM {$table_name} WHERE name='{$obj_name}' AND prob>{$prob}";
            $sql_ret = mysqli_query($conn, $query_str) or die("DB Query Failed");

            $result = new stdClass();
            $result->n_image = 0;
            $result->images = [];

            while ($row = mysqli_fetch_array($sql_ret))
            {
                $result->n_image += 1;
                array_push($result->images, $row);
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
                let canvas_node = document.createElement("canvas");
                let ctx = canvas_node.getContext("2d");
                let url_text_node = document.createTextNode(`image URL : ${img.url}`);
                let class_text_node = document.createTextNode(`class_name : ${img.name}`);
                let prob_text_node = document.createTextNode(`prob : ${img.prob}`);

                let img_obj = new Image;

                img_obj.src = img.url;


                img_obj.onload = function() 
                { 
                    canvas_node.width = this.width;
                    canvas_node.height = this.height;

                    ctx.drawImage(img_obj, 0, 0);

                    // Red rectangle
                    ctx.beginPath();
                    ctx.lineWidth = "3";
                    ctx.strokeStyle = "red";
                    ctx.rect(img.x_min_idx, img.y_min_idx, img.box_width, img.box_height);
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
        </script>

    </body>
</html>