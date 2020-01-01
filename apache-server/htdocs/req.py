import pickle
import PIL.Image
import numpy
import requests
import sys
from io import BytesIO
import json


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("wrong URL", sys.argv[1])
    else:
        img_url = sys.argv[1]

        res_image = requests.get(img_url)
        image = img = PIL.Image.open(BytesIO(res_image.content))
        image_np = numpy.array(image)

        payload = {"instances": [image_np.tolist()]}

        # You should do something!
        model_url = ""
        res = requests.post(model_url, json=payload)

        result = json.loads(res.content.decode('utf-8'))

        scores = result['predictions'][0]['detection_scores']
        boxes = result['predictions'][0]['detection_boxes']
        classes = result['predictions'][0]['detection_classes']
        n_tot_detected = int(result['predictions'][0]['num_detections'])

        label = ["person", "bicycle", "car", "motorcycle", "airplane", "bus", "train",
                "truck", "boat", "traffic light", "fire hydrant", "street sign", "stop sign", 
                "parking meter", "bench", "bird", "cat", "dog", "horse", "sheep", "cow", 
                "elephant", "bear", "zebra", "giraffe", "hat", "backpack", "umbrella", "shoe",
                "eye glasses", "handbag", "tie", "suitcase", "frisbee", "skis", "snowboard",
                "sports ball", "kite", "baseball bat", "baseball glove", "skateboard", "surfboard",
                "tennis racket", "bottle", "plate", "wine glass", "cup", "fork", "knife", "spoon", 
                "bowl", "banana", "apple", "sandwich", "orange", "broccoli", "carrot", "hot dog",
                "pizza", "donut", "cake", "chair", "couch", "potted plant", "bed", "mirror", "dining table",
                "window", "desk", "toilet", "door", "tv", "laptop", "mouse", "remote", "keyboard", "cell phone",
                "microwave", "oven", "toaster", "sink", "refrigerator", "blender", "book", "clock", "vase", 
                "scissors", "teddy bear", "hair drier", "toothbrush", "hair brush"]

        n_row, n_col, _ = image_np.shape

        n_detected = 0

        for i in range(n_tot_detected):
            if scores[i] > 0.9:
                n_detected += 1
        
        print(f"n_detected={n_detected}")

        for i in range(n_tot_detected):
            if scores[i] > 0.9:
                y_min_idx = int(boxes[i][0] * n_row)
                x_min_idx = int(boxes[i][1] * n_col)
                y_max_idx = int(boxes[i][2] * n_row)
                x_max_idx = int(boxes[i][3] * n_col)

                box_width = x_max_idx - x_min_idx
                box_height = y_max_idx - y_min_idx

                box_data = f"name={label[int(classes[i]) - 1]}&prob={scores[i]}" \
                            + f"&y_min_idx={y_min_idx}&x_min_idx={x_min_idx}" \
                            + f"&box_height={box_height}&box_width={box_width}" \
                            + f"&n_row={n_row}&n_col={n_col}"

                print(box_data)
        