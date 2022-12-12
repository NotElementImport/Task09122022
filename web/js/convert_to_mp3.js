let AUDIO_CONTEXT = new OfflineAudioContext(2, 44800 * 1, 44800);

let buffer_to_submit = null;

const form_submit = () => {

    var param = document.querySelector('meta[name=csrf-param]').getAttribute("content");
    var token = document.querySelector('meta[name=csrf-token]').getAttribute("content");

    let Name = document.getElementById('autoSizingInput').value;
    let Genres = document.getElementById('genres');

    let f_FormData = new FormData();
    f_FormData.append(param, token);
    f_FormData.append('name', Name);
    f_FormData.append('genre', Genres.options[Genres.selectedIndex].value);
    f_FormData.append('song', buffer_to_submit, 'sing_song.mp3');

    console.log(buffer_to_submit);

    fetch('', {
        method : 'post',
        body : f_FormData,
        headers: {
          'X-CSRF-Token': token
        }
    }).then(e => e.json()).then(e => {
        const myModal = new bootstrap.Modal('#Error', {
          keyboard: false
        });
        
        let Text = document.getElementById('TextError');

        if(e.Header.Code == -1)
        {
            Text.innerHTML = e.Header.Message;     
        }
        else
        {
            Text.innerHTML = "File uploaded!";
        }
        myModal.show();
    });
};

let ButtonUpload = document.querySelector('button#ButtonUpload');

document.getElementById('FileUpload').parentElement.addEventListener('drop', ev => {
    ev.preventDefault();
    let file = ev.dataTransfer.items[0].getAsFile();
    open_file(file);
});

let modal = null;

const open_file = async (File = null) => {
    if(modal == null)
    {
        modal = new bootstrap.Modal('#Wait', {
            keyboard: false
        });
    }
    let FileUpload = document.getElementById('FileUpload');
    let file = (File == null ? FileUpload.files[0]  : File);
    let mime = file.name.split('.');
    mime = mime[mime.length-1].toLowerCase();
    
    let types = ['mp3', 'wav', 'ogg'];
    if(types.includes(mime) == false)
    {
        const myModal = new bootstrap.Modal('#Error', {
          keyboard: false
        });
        
        let Text = document.getElementById('TextError');
        Text.innerHTML = "File format is not audio! Or not supported";

        myModal.show();
        return;
    }
    else
    {
        modal.show();
    }

    if(!ButtonUpload.classList.contains('disabled'))
    {
        ButtonUpload.classList.add('disabled');
    }


    let Reader = new FileReader();

    Reader.onload = async (ev) => {

        AUDIO_CONTEXT.decodeAudioData(ev.target.result).then(function(buffer) {
          console.log(buffer.sampleRate);
          AUDIO_CONTEXT = new OfflineAudioContext(2, 44100 * buffer.duration, 44100);
          let BufferSample = AUDIO_CONTEXT.createBufferSource();
              BufferSample.buffer = buffer;

              BufferSample.start(0);
              BufferSample.connect(AUDIO_CONTEXT.destination);

              AUDIO_CONTEXT.startRendering().then(async renderedBuffer =>{
                  audioBufferToWav(renderedBuffer);
                  modal.hide();
              }).catch((err) => {
                  console.log(err);
              });
        });
    };
    Reader.readAsArrayBuffer(file);
    
    let LabelSong = document.getElementById('autoSizingInput');

    LabelSong.value = file.name.split('.')[0];
};

function audioBufferToWav(aBuffer) {
    let numOfChan = aBuffer.numberOfChannels,
      btwLength = aBuffer.length * numOfChan * 2 + 44,
      btwArrBuff = new ArrayBuffer(btwLength),
      btwView = new DataView(btwArrBuff),
      btwChnls = [],
      btwIndex,
      btwSample,
      btwOffset = 0,
      btwPos = 0;
    setUint32(0x46464952); // "RIFF"
    setUint32(btwLength - 8); // file length - 8
    setUint32(0x45564157); // "WAVE"
    setUint32(0x20746d66); // "fmt " chunk
    setUint32(16); // length = 16
    setUint16(1); // PCM (uncompressed)
    setUint16(numOfChan);
    setUint32(aBuffer.sampleRate);
    setUint32(aBuffer.sampleRate * 2 * numOfChan); // avg. bytes/sec
    setUint16(numOfChan * 2); // block-align
    setUint16(16); // 16-bit
    setUint32(0x61746164); // "data" - chunk
    setUint32(btwLength - btwPos - 4); // chunk length
  
    for (btwIndex = 0; btwIndex < aBuffer.numberOfChannels; btwIndex++)
      btwChnls.push(aBuffer.getChannelData(btwIndex));
  
    while (btwPos < btwLength) {
      for (btwIndex = 0; btwIndex < numOfChan; btwIndex++) {
        // interleave btwChnls
        btwSample = Math.max(-1, Math.min(1, btwChnls[btwIndex][btwOffset])); // clamp
        btwSample =
          (0.5 + btwSample < 0 ? btwSample * 32768 : btwSample * 32767) | 0; // scale to 16-bit signed int
        btwView.setInt16(btwPos, btwSample, true); // write 16-bit sample
        btwPos += 2;
      }
      btwOffset++; // next source sample
    }
  
    let wavHdr = lamejs.WavHeader.readHeader(new DataView(btwArrBuff));
  
    //Stereo
    let data = new Int16Array(btwArrBuff, wavHdr.dataOffset, wavHdr.dataLen / 2);
    let leftData = [];
    let rightData = [];
    for (let i = 0; i < data.length; i += 2) {
      leftData.push(data[i]);
      rightData.push(data[i + 1]);
    }
    var left = new Int16Array(leftData);
    var right = new Int16Array(rightData);
  
    //STEREO
    if (wavHdr.channels === 2)
    return wavToMp3(
        wavHdr.channels,
        wavHdr.sampleRate,
        left,
        right,
    );
    //MONO
    else if (wavHdr.channels === 1)
    return wavToMp3(wavHdr.channels, wavHdr.sampleRate, data);
  
    function setUint16(data) {
      btwView.setUint16(btwPos, data, true);
      btwPos += 2;
    }
  
    function setUint32(data) {
      btwView.setUint32(btwPos, data, true);
      btwPos += 4;
    }
}

function wavToMp3(channels, sampleRate, left, right = null) {
    var buffer = [];
    var mp3enc = new lamejs.Mp3Encoder(channels, sampleRate, 128);
    var remaining = left.length;
    var samplesPerFrame = 1152;
  
    for (var i = 0; remaining >= samplesPerFrame; i += samplesPerFrame) {
      if (!right) {
        var mono = left.subarray(i, i + samplesPerFrame);
        var mp3buf = mp3enc.encodeBuffer(mono);
      } else {
        var leftChunk = left.subarray(i, i + samplesPerFrame);
        var rightChunk = right.subarray(i, i + samplesPerFrame);
        var mp3buf = mp3enc.encodeBuffer(leftChunk, rightChunk);
      }
      if (mp3buf.length > 0) {
        buffer.push(mp3buf); //new Int8Array(mp3buf));
      }
      remaining -= samplesPerFrame;
    }
    var d = mp3enc.flush();
    if (d.length > 0) {
      buffer.push(new Int8Array(d));
    }
  
    var mp3Blob = new Blob(buffer, { type: "audio/mp3" })
    var bUrl = window.URL.createObjectURL(mp3Blob);

    buffer_to_submit = mp3Blob;
    ButtonUpload.classList.remove('disabled');

    let Sounds = document.getElementById('Sounds');
    Sounds.src = bUrl;

    // send the download link to the console
    console.log('mp3 download:', bUrl);

}