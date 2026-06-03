(function(){
  const canvasElement = document.getElementById('card-canvas');
  if (!canvasElement || typeof fabric === 'undefined') return;

  const width = parseInt(canvasElement.dataset.width || '1013', 10);
  const height = parseInt(canvasElement.dataset.height || '638', 10);
  const initialJson = document.getElementById('design_json')?.value;
  const backgroundUrl = canvasElement.dataset.background;
  const canvas = new fabric.Canvas('card-canvas', { width, height, backgroundColor: '#ffffff' });

  function fitCanvasOnScreen(){
    const container = document.querySelector('.canvas-wrap');
    if (!container) return;
    const maxWidth = Math.max(320, container.clientWidth - 40);
    const zoom = Math.min(1, maxWidth / width);
    canvas.setZoom(zoom);
    canvas.setDimensions({ width: width * zoom, height: height * zoom }, { cssOnly: true });
  }

  function setBackground(url){
    if (!url) return;
    fabric.Image.fromURL(url, function(img){
      img.scaleToWidth(width);
      img.scaleToHeight(height);
      canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
    }, {crossOrigin: 'anonymous'});
  }

  window.addField = function(field, label){
    if (field === 'photo') {
      const rect = new fabric.Rect({ left: 20, top: 20, width: 210, height: 260, fill: '#f3f4f6', stroke: '#111827', strokeDashArray: [8, 6] });
      rect.data = { field: 'photo', type: 'photo' };
      canvas.add(rect);
      canvas.setActiveObject(rect);
      return;
    }
    const text = new fabric.Text(label || field, { left: 20, top: 20, fontSize: 32, fontFamily: 'Arial', fill: '#111827' });
    text.data = { field, type: 'text' };
    canvas.add(text);
    canvas.setActiveObject(text);
  }

  window.saveDesign = function(){
    const json = canvas.toJSON(['data']);
    document.getElementById('design_json').value = JSON.stringify(json);
    document.getElementById('designer-form').submit();
  }

  const fontSizeInput = document.getElementById('font-size');
  const colorInput = document.getElementById('font-color');
  if (fontSizeInput) fontSizeInput.addEventListener('input', () => {
    const obj = canvas.getActiveObject();
    if (obj && obj.type === 'text') { obj.set('fontSize', parseInt(fontSizeInput.value, 10)); canvas.renderAll(); }
  });
  if (colorInput) colorInput.addEventListener('input', () => {
    const obj = canvas.getActiveObject();
    if (obj && obj.type === 'text') { obj.set('fill', colorInput.value); canvas.renderAll(); }
  });

  if (initialJson) {
    try {
      canvas.loadFromJSON(JSON.parse(initialJson), () => { setBackground(backgroundUrl); canvas.renderAll(); fitCanvasOnScreen(); });
    } catch(e) { setBackground(backgroundUrl); }
  } else {
    setBackground(backgroundUrl);
  }
  fitCanvasOnScreen();
  window.addEventListener('resize', fitCanvasOnScreen);
})();
