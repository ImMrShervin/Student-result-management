import { api } from '@/lib/api';
import { useAuth } from '@/stores/auth';
import { FileText, Download } from 'lucide-react';
import toast from 'react-hot-toast';
import { useState } from 'react';

export default function Transcripts() {
  const user = useAuth((s) => s.user);
  const studentId = user?.student?.id;
  const [generated, setGenerated] = useState<any>(null);
  const [loading, setLoading] = useState(false);
  const [downloading, setDownloading] = useState(false);

  const generate = async () => {
    if (!studentId) { toast.error('No student profile'); return; }
    setLoading(true);
    try {
      const { data } = await api.post(`/students/${studentId}/transcript`);
      setGenerated(data);
      toast.success('Transcript generated');
    } catch (e: any) {
      toast.error(e?.response?.data?.message || 'Failed');
    } finally { setLoading(false); }
  };

  const download = async () => {
    if (!generated) return;
    setDownloading(true);
    try {
      const { data, headers } = await api.get(`/transcripts/${generated.id}/download`, {
        responseType: 'blob',
      });
      const filename = headers['content-disposition']
        ?.match(/filename\*?=(?:UTF-8'')?([^;\n]+)/i)?.[1]
        ?.replace(/['"]/g, '')
        ?? `transcript-${generated.verification_code}.pdf`;
      const url = URL.createObjectURL(data);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    } catch (e: any) {
      toast.error(e?.response?.data?.message || 'Download failed');
    } finally { setDownloading(false); }
  };

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold flex items-center gap-2"><FileText /> Transcripts</h1>

      <div className="card">
        <h3 className="font-semibold mb-2">Generate Official Transcript</h3>
        <p className="text-sm text-slate-500 mb-4">
          A PDF transcript will be generated with a QR verification code embedded. Anyone can verify its authenticity by scanning the QR or visiting the verify URL.
        </p>
        <button onClick={generate} disabled={loading} className="btn-primary">
          {loading ? 'Generating…' : 'Generate My Transcript'}
        </button>
      </div>

      {generated && (
        <div className="card">
          <h3 className="font-semibold mb-2">✓ Ready</h3>
          <div className="text-sm space-y-2">
            <div><span className="text-slate-500">Verification Code:</span> <code className="font-mono">{generated.verification_code}</code></div>
            <div><a className="text-brand-600 underline" href={generated.verify_url} target="_blank" rel="noreferrer">Public verify page</a></div>
            <button onClick={download} disabled={downloading} className="btn-primary mt-2 inline-flex">
              <Download className="w-4 h-4" /> {downloading ? 'Downloading…' : 'Download PDF'}
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
