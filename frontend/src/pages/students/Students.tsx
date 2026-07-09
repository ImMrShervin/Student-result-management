import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { DataTable } from '@/components/ui/DataTable';
import { useState } from 'react';
import { Search } from 'lucide-react';

export default function Students() {
  const [q, setQ] = useState('');
  const { data, isLoading } = useQuery({
    queryKey: ['students', q],
    queryFn: async () => (await api.get('/students', { params: { q, per_page: 20 } })).data,
  });

  const rows = data?.data ?? [];

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Students</h1>
        <div className="relative">
          <Search className="w-4 h-4 absolute left-3 top-3 text-slate-400" />
          <input value={q} onChange={(e) => setQ(e.target.value)} placeholder="Search by name, number..." className="input pl-9 w-64" />
        </div>
      </div>

      <DataTable
        loading={isLoading}
        rows={rows}
        columns={[
          { key: 'student_number', header: 'Student #', render: (r: any) => <span className="font-mono">{r.student_number}</span> },
          { key: 'name', header: 'Name', render: (r: any) => `${r.user?.first_name} ${r.user?.last_name}` },
          { key: 'department', header: 'Department', render: (r: any) => r.department?.name },
          { key: 'entry_year', header: 'Entry Year' },
          { key: 'cumulative_gpa', header: 'GPA', render: (r: any) => <span className="badge bg-blue-100 text-blue-800">{Number(r.cumulative_gpa).toFixed(2)}</span> },
          { key: 'status', header: 'Status', render: (r: any) => <span className="badge bg-slate-100 dark:bg-slate-700">{r.academic_status}</span> },
        ]}
      />

      {data?.meta && (
        <div className="text-sm text-slate-500">
          Showing {data.meta.from}–{data.meta.to} of {data.meta.total}
        </div>
      )}
    </div>
  );
}
