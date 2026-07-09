import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api';
import { Bar, Doughnut } from 'react-chartjs-2';
import { Chart as ChartJS, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

export default function Reports() {
  const passFail = useQuery({ queryKey: ['pass-vs-fail'], queryFn: async () => (await api.get('/reports/pass-vs-fail')).data });
  const dist = useQuery({ queryKey: ['grade-dist'], queryFn: async () => (await api.get('/reports/grade-distribution')).data });
  const deptStats = useQuery({ queryKey: ['dept-stats'], queryFn: async () => (await api.get('/reports/department-stats')).data });
  const trend = useQuery({ queryKey: ['trend'], queryFn: async () => (await api.get('/reports/enrollment-trend')).data });

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Reports & Analytics</h1>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {passFail.data && (
          <div className="card">
            <h3 className="font-semibold mb-4">Pass vs Fail</h3>
            <Doughnut
              data={{
                labels: ['Pass', 'Fail'],
                datasets: [{ data: [passFail.data.pass, passFail.data.fail], backgroundColor: ['#10b981', '#ef4444'] }],
              }}
            />
          </div>
        )}

        {dist.data?.length > 0 && (
          <div className="card">
            <h3 className="font-semibold mb-4">Grade Distribution</h3>
            <Bar
              data={{
                labels: dist.data.map((g: any) => g.letter_grade),
                datasets: [{ data: dist.data.map((g: any) => g.count), backgroundColor: '#6366f1', borderRadius: 6 }],
              }}
              options={{ plugins: { legend: { display: false } } }}
            />
          </div>
        )}

        {deptStats.data?.length > 0 && (
          <div className="card lg:col-span-2">
            <h3 className="font-semibold mb-4">Department Overview</h3>
            <div className="overflow-x-auto">
              <table className="table">
                <thead><tr><th>Department</th><th>Students</th><th>Teachers</th><th>Courses</th><th>Avg GPA</th></tr></thead>
                <tbody>
                  {deptStats.data.map((d: any, i: number) => (
                    <tr key={i}>
                      <td>{d.name}</td><td>{d.students}</td><td>{d.teachers}</td><td>{d.courses}</td>
                      <td><span className="badge bg-brand-100 text-brand-800">{Number(d.avg_gpa ?? 0).toFixed(2)}</span></td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {trend.data?.length > 0 && (
          <div className="card lg:col-span-2">
            <h3 className="font-semibold mb-4">Enrollment Trend (Monthly)</h3>
            <Bar
              data={{
                labels: trend.data.map((t: any) => t.month),
                datasets: [{ data: trend.data.map((t: any) => t.count), backgroundColor: '#0ea5e9', borderRadius: 6 }],
              }}
              options={{ plugins: { legend: { display: false } } }}
            />
          </div>
        )}
      </div>
    </div>
  );
}
