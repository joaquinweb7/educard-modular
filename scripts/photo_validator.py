import sys
import os
import json
import cv2
import numpy as np

def validate_photo(image_path):
    results = {
        "success": False,
        "details": {
            "red_background": False,
            "face_detected": False,
            "aspect_ratio_1_1": False,
            "formal_attire_heuristic": False,
            "hair_heuristic": False
        },
        "message": ""
    }

    if not os.path.exists(image_path):
        results["message"] = "Image file not found."
        return results

    # Load image
    img = cv2.imread(image_path)
    if img is None:
        results["message"] = "Invalid image file."
        return results

    height, width, _ = img.shape

    # 1. Aspect Ratio (1:1 with 5% tolerance)
    ratio = width / height
    if 0.95 <= ratio <= 1.05:
        results["details"]["aspect_ratio_1_1"] = True

    # 2. Red Background Detection (More forgiving)
    hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)
    # Broaden red range to include darker reds and slight orange-reds
    lower_red1 = np.array([0, 50, 40])
    upper_red1 = np.array([15, 255, 255])
    lower_red2 = np.array([165, 50, 40])
    upper_red2 = np.array([180, 255, 255])

    mask1 = cv2.inRange(hsv, lower_red1, upper_red1)
    mask2 = cv2.inRange(hsv, lower_red2, upper_red2)
    red_mask = mask1 + mask2

    # Check the top and side borders, but ignore the very bottom where clothes are
    border_pixels = np.concatenate((
        red_mask[0:30, :].flatten(),        # top border
        red_mask[0:int(height*0.7), 0:30].flatten(),  # upper left border
        red_mask[0:int(height*0.7), -30:].flatten()   # upper right border
    ))

    red_ratio = np.count_nonzero(border_pixels) / len(border_pixels)
    if red_ratio > 0.35: # Much more forgiving threshold
        results["details"]["red_background"] = True

    # 3. Face Detection
    cascade_path = cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'
    face_cascade = cv2.CascadeClassifier(cascade_path)
    
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    # Equalize histogram to improve face detection in bad lighting
    gray_eq = cv2.equalizeHist(gray)
    
    faces = face_cascade.detectMultiScale(gray_eq, scaleFactor=1.05, minNeighbors=4, minSize=(80, 80))
    if len(faces) == 0:
        # Fallback to non-equalized
        faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=3, minSize=(80, 80))

    if len(faces) > 0:
        results["details"]["face_detected"] = True
        
        # Take the largest face
        x, y, w, h = max(faces, key=lambda rect: rect[2] * rect[3])
        
        # 4. Formal Attire Heuristic
        chest_roi_y1 = min(height, y + h)
        chest_roi_y2 = min(height, chest_roi_y1 + int(h * 1.5))
        chest_roi_x1 = max(0, x - int(w/1.5))
        chest_roi_x2 = min(width, x + w + int(w/1.5))
        
        if chest_roi_y2 > chest_roi_y1 and chest_roi_x2 > chest_roi_x1:
            chest_roi = img[chest_roi_y1:chest_roi_y2, chest_roi_x1:chest_roi_x2]
            chest_hsv = cv2.cvtColor(chest_roi, cv2.COLOR_BGR2HSV)
            
            # Check for dark clothes (suit) OR white clothes (shirt) OR light blue/gray
            # We just want to ensure there is some contrast or uniform clothing
            dark_mask = cv2.inRange(chest_hsv, np.array([0, 0, 0]), np.array([180, 255, 85]))
            white_mask = cv2.inRange(chest_hsv, np.array([0, 0, 160]), np.array([180, 40, 255]))
            
            formal_pixels = cv2.countNonZero(dark_mask) + cv2.countNonZero(white_mask)
            total_chest_pixels = chest_roi.shape[0] * chest_roi.shape[1]
            
            # Very forgiving: if at least 5% of the chest area matches formal colors, pass it
            if total_chest_pixels > 0 and (formal_pixels / total_chest_pixels) > 0.05:
                 results["details"]["formal_attire_heuristic"] = True
            else:
                 # If color fails, try edge detection to see if there is a collar/tie structure
                 edges = cv2.Canny(chest_roi, 50, 150)
                 edge_density = np.count_nonzero(edges) / total_chest_pixels
                 if edge_density > 0.02: # If there's structure, give it a pass
                     results["details"]["formal_attire_heuristic"] = True
                 
        # 5. Hair Heuristic
        # Just check if the top of the head is within the frame and not cut off too harshly
        hair_roi_y1 = max(0, y - int(h * 0.8))
        hair_roi_y2 = y
        hair_roi_x1 = max(0, x - int(w * 0.2))
        hair_roi_x2 = min(width, x + w + int(w * 0.2))
        
        if hair_roi_y2 > hair_roi_y1 and hair_roi_x2 > hair_roi_x1:
            # We assume it's presentable if the face is properly framed (hair fits in the upper region)
            # and there's no excessive redness (meaning it's not transparent/missing)
            hair_roi = red_mask[hair_roi_y1:hair_roi_y2, hair_roi_x1:hair_roi_x2]
            hair_red_ratio = np.count_nonzero(hair_roi) / (hair_roi.shape[0] * hair_roi.shape[1])
            if hair_red_ratio < 0.8: # If it's not 80% background, there is hair/head there
                results["details"]["hair_heuristic"] = True

    # Always return success=True if the analysis completed without exception.
    results["success"] = True
    
    if (results["details"]["aspect_ratio_1_1"] and 
        results["details"]["red_background"] and 
        results["details"]["face_detected"]):
        results["message"] = "La imagen cumple con los requisitos mínimos."
    else:
        results["message"] = "La imagen no cumple con todos los requisitos. Requiere revisión manual."

    return results

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "message": "No image path provided."}))
        sys.exit(1)

    image_path = sys.argv[1]
    output = validate_photo(image_path)
    print(json.dumps(output))
