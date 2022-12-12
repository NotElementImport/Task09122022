var AudioSources = [];
const track_playlist = document.getElementById('track_playlist');

let CurrentPlayId = -1;
let CurrentPlayElement = null;

/**
*   Load Track and create Elements
*   link_array be like = [ {name : "", track : "", image : ""} ] // Pseudo json
*/

const TryCreateQueryToLoadTracks = async (Page, Limit = 5) => {
    let error = false;

    track_playlist.innerHTML = "";

    if(CurrentPlayId != -1)
    {
        AudioSources[CurrentPlayId].pause();      
    }

    AudioSources = [];

    CurrentPlayId = -1;
    CurrentPlayElement = null;

    var param = document.querySelector('meta[name=csrf-param]').getAttribute("content");
    var token = document.querySelector('meta[name=csrf-token]').getAttribute("content");

    let genres = document.querySelector('select#genres');

    let Form = new FormData();
    Form.append(param, token);
    Form.append('page', Page);
    Form.append('genre', genres.options[genres.selectedIndex].value);

    fetch('/',{
        method : 'post',
        body : Form
    }).then(e => e.json()).then(e =>{
        LoadTracks(e);
    });

    if(error)
    {
        const myModal = new bootstrap.Modal('#Error', {
            keyboard: false
        });

        let Text = document.getElementById('TextError');
        Text.innerHTML = "Tracks cannot be loaded";

        myModal.show();
    }
};

const LoadTracks = async (links_array = null) => {

    let UniqIds = 0;

    links_array.forEach(elements => {

        let Template = `
            <div class="row g-0">
                <div class="div-img border-end rounded-start col-md-3" style="background-image: url('${elements.image}');">
                    
                </div>
                <div class="col">
                    <div class="card-body" style="text-align: left;">
                        <h5 class="card-title p-2">${elements.name}</h5>
                        <div class="p-2 border rounded">
                            <div class="WaveForm${UniqIds}" style="height: 128px; position: relative;">
                                <div class="duration Shadow border rounded">
                                    <span id="Duration${UniqIds}"> </span>
                                </div>
                                <div class="controller row g-0">
                                    <div class="col">
                                        <button onclick="ToggleAudio(${UniqIds}, this)" class="t_load${UniqIds} disabled btn btn-primary Shadow circle ButtonStop${UniqIds}">
                                            <i class="bi bi-play"></i>
                                        </button>
                                        <button onclick="StopAudio(${UniqIds}, this)" class="t_load${UniqIds} disabled btn btn-light Shadow circle border">
                                            <i class="bi bi-stop"></i>
                                        </button>
                                    </div>   
                                    <div class="col text-end">
                                        <small class="text-muted background Shadow border rounded"> ${elements.genre} </small>
                                    </div>
                                </div>
                                <span class="message-on-middle" id="t_load${UniqIds}">
                                    Loading...
                                </span>
                            </div>
                        </div>
                        <small class="text-muted"> Upload by: ${elements.who} </small>
                    </div>
                </div>
            </div>
        `;

        let ElementMusicShowcase = document.createElement('div');
        ElementMusicShowcase.innerHTML = Template;
        ElementMusicShowcase.setAttribute('class','card mb-3');

        track_playlist.appendChild(ElementMusicShowcase);

        let Result = LoadAudio(UniqIds, elements.track);

        UniqIds += 1;

    });
};

let LoadAudio = async (id = 0, name_track = "") => {
    AudioSources.push(WaveSurfer.create({
        container: document.getElementsByClassName(`WaveForm${id}`)[0],
        waveColor: '#0d6efd',
        progressColor: 'purple',
        barWidth: 2,
        barHeight: 1,
        barGap: null
    }));

    let ButtonToggle = document.getElementsByClassName(`ButtonStop${id}`)[0];
    let LoadDescription = document.getElementById(`t_load${id}`);

    AudioSources[id].on('finish', () => { 
        StopAudio(id, ButtonToggle);
    });

    AudioSources[id].load(`${name_track}`);

    AudioSources[id].on('ready', () => {
        let Duration = Math.floor(AudioSources[id].getDuration());
        let Minute = Math.floor(Duration * (1 / 60));

        let SpanDuration = document.getElementById(`Duration${id}`);

        let ButtonsQuery = Array.from(document.querySelectorAll(`button.t_load${id}`));

        console.log(ButtonsQuery);

        ButtonsQuery.forEach(Element => {
            Element.classList.remove('disabled', `t_load${id}`);
        });

        LoadDescription.remove();

        SpanDuration.innerHTML = Minute + ":" + (Duration % 60);
    });

};

const ToggleAudio = (audio_route_id = 0, element = null) => {

    AudioSources[audio_route_id].play();

    element.childNodes[1].setAttribute('class', 'bi bi-pause');

    if(CurrentPlayId != -1 && CurrentPlayId != audio_route_id)
    {
        AudioSources[CurrentPlayId].pause();
        CurrentPlayElement.childNodes[1].setAttribute('class', 'bi bi-play');
    }
    else if (CurrentPlayId == audio_route_id)
    {
        AudioSources[CurrentPlayId].pause();
        CurrentPlayElement.childNodes[1].setAttribute('class', 'bi bi-play');
        
        console.log('pause');

        CurrentPlayElement = null;
        CurrentPlayId = -1;

        return;
    }

    CurrentPlayId = audio_route_id;
    CurrentPlayElement = element;
};

const StopAudio = (audio_route_id = 0, element) => {
    if (CurrentPlayId == audio_route_id)
    {
        CurrentPlayElement.childNodes[1].setAttribute('class', 'bi bi-play');

        CurrentPlayElement = null;
        CurrentPlayId = -1;
    }

    AudioSources[audio_route_id].seekTo(0);
    AudioSources[audio_route_id].pause();

};

// LoadTracks([
//     {
//         name : "Blues 1",
//         track : "Blues_Vibe_5.wav",
//         image : "GrabOneHand.png",
//         genre : "Pop/Blues"
//     },
//     {
//         name : "Blues 2",
//         track : "Blues_Vibe_4.wav",
//         image : "GrabOneHand.png",
//         genre : "Pop/Blues"
//     },
//     {
//         name : "Очко джефри",
//         track : "1IziL- After Block.mp3",
//         image : "nEtn0AOuz2s.png",
//         genre : "Hip-Hop"
//     }
// ]);