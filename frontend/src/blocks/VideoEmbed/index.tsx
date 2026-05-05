import type { ComponentConfig } from '@measured/puck';

export type VideoEmbedProps = {
  heading?: string;
  subtitle?: string;
  videoUrl: string;
  videoType: 'youtube' | 'vimeo' | 'file';
  poster?: string;
  autoplay: boolean;
  controls: boolean;
  aspectRatio: '16:9' | '4:3' | '21:9';
};

const ratioMap: Record<VideoEmbedProps['aspectRatio'], string> = {
  '16:9': 'aspect-[16/9]',
  '4:3': 'aspect-[4/3]',
  '21:9': 'aspect-[21/9]',
};

const getYoutubeId = (url: string) => {
  const m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/);
  return m ? m[1] : url;
};

const getVimeoId = (url: string) => {
  const m = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
  return m ? m[1] : url;
};

const VideoEmbedRender = ({
  heading,
  subtitle,
  videoUrl,
  videoType,
  poster,
  autoplay,
  controls,
  aspectRatio,
}: VideoEmbedProps) => {
  let embed: React.ReactNode = null;

  if (videoType === 'youtube') {
    const id = getYoutubeId(videoUrl);
    const params = new URLSearchParams({
      autoplay: autoplay ? '1' : '0',
      controls: controls ? '1' : '0',
      mute: autoplay ? '1' : '0',
      rel: '0',
    });
    embed = (
      <iframe
        src={`https://www.youtube.com/embed/${id}?${params.toString()}`}
        className="w-full h-full"
        frameBorder={0}
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowFullScreen
      />
    );
  } else if (videoType === 'vimeo') {
    const id = getVimeoId(videoUrl);
    const params = new URLSearchParams({
      autoplay: autoplay ? '1' : '0',
      muted: autoplay ? '1' : '0',
      controls: controls ? '1' : '0',
    });
    embed = (
      <iframe
        src={`https://player.vimeo.com/video/${id}?${params.toString()}`}
        className="w-full h-full"
        frameBorder={0}
        allow="autoplay; fullscreen; picture-in-picture"
        allowFullScreen
      />
    );
  } else {
    embed = (
      <video
        src={videoUrl}
        poster={poster}
        autoPlay={autoplay}
        muted={autoplay}
        controls={controls}
        playsInline
        loop
        className="w-full h-full object-cover"
      />
    );
  }

  return (
    <section className="w-full bg-black py-20">
      <div className="max-w-6xl mx-auto px-6">
        {(heading || subtitle) && (
          <div className="text-center mb-12">
            {heading && (
              <h2 className="font-serif text-3xl md:text-5xl font-light text-white mb-4">
                {heading}
              </h2>
            )}
            {subtitle && (
              <p className="text-base md:text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
                {subtitle}
              </p>
            )}
          </div>
        )}
        <div className={`relative w-full ${ratioMap[aspectRatio]} overflow-hidden bg-neutral-900`}>
          {embed}
        </div>
      </div>
    </section>
  );
};

export const VideoEmbed: { config: ComponentConfig<VideoEmbedProps> } = {
  config: {
    label: 'Video Embed',
    fields: {
      heading: { type: 'text', label: 'Encabezado' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      videoUrl: { type: 'text', label: 'URL del vídeo' },
      videoType: {
        type: 'select',
        label: 'Tipo',
        options: [
          { label: 'YouTube', value: 'youtube' },
          { label: 'Vimeo', value: 'vimeo' },
          { label: 'Archivo propio', value: 'file' },
        ],
      },
      poster: { type: 'text', label: 'Poster (para file)' },
      autoplay: {
        type: 'radio',
        label: 'Autoplay',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      controls: {
        type: 'radio',
        label: 'Controles',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      aspectRatio: {
        type: 'select',
        label: 'Aspect ratio',
        options: [
          { label: '16:9', value: '16:9' },
          { label: '4:3', value: '4:3' },
          { label: '21:9 Cinemático', value: '21:9' },
        ],
      },
    },
    defaultProps: {
      heading: 'Nuestra historia',
      subtitle: 'Descubre el proceso artesanal detrás de cada detalle.',
      videoUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
      videoType: 'youtube',
      poster: '',
      autoplay: false,
      controls: true,
      aspectRatio: '16:9',
    },
    render: VideoEmbedRender,
  },
};
