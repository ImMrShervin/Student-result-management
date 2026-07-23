import { useEffect } from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';
import Login from './pages/auth/Login';
import DashboardLayout from './components/layout/DashboardLayout';
import Dashboard from './pages/dashboard/Dashboard';
import Students from './pages/students/Students';
import Teachers from './pages/teachers/Teachers';
import Courses from './pages/courses/Courses';
import Enrollments from './pages/enrollments/Enrollments';
import Grades from './pages/grades/Grades';
import Transcripts from './pages/transcripts/Transcripts';
import Reports from './pages/reports/Reports';
import Faculties from './pages/admin/Faculties';
import Departments from './pages/admin/Departments';
import { useAuth } from './stores/auth';

function RequireAuth({ children }: { children: JSX.Element }) {
  const token = useAuth((s) => s.token);
  if (!token) return <Navigate to="/login" replace />;
  return children;
}

function RedirectIfAuth({ children }: { children: JSX.Element }) {
  const token = useAuth((s) => s.token);
  if (token) return <Navigate to="/dashboard" replace />;
  return children;
}

export default function App() {
  const token = useAuth((s) => s.token);
  const fetchMe = useAuth((s) => s.fetchMe);

  useEffect(() => {
    if (token) fetchMe();
  }, [token, fetchMe]);

  return (
    <Routes>
      <Route path="/login" element={<RedirectIfAuth><Login /></RedirectIfAuth>} />
      <Route path="/" element={<RequireAuth><DashboardLayout /></RequireAuth>}>
        <Route index element={<Navigate to="/dashboard" replace />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="students" element={<Students />} />
        <Route path="teachers" element={<Teachers />} />
        <Route path="courses" element={<Courses />} />
        <Route path="enrollments" element={<Enrollments />} />
        <Route path="grades" element={<Grades />} />
        <Route path="transcripts" element={<Transcripts />} />
        <Route path="reports" element={<Reports />} />
        <Route path="faculties" element={<Faculties />} />
        <Route path="departments" element={<Departments />} />
      </Route>
      <Route path="*" element={<div className="p-10 text-center">404 — Not found</div>} />
    </Routes>
  );
}
