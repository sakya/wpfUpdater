using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;
using System.Diagnostics;
using CsUpdater;

namespace WpfAutoUpdater
{
  /// <summary>
  /// Logica di interazione per MainWindow.xaml
  /// </summary>
  public partial class MainWindow : Window
  {
    Updater m_Updater = null;
    UpdaterApp m_App = null;

    public MainWindow()
    {
      InitializeComponent();

      m_Updater = new Updater(null, string.Empty, "Windows");
      m_Updater.CheckCompletedDelegate += CheckCompleted;
      m_Updater.DownloadingDelegate += Download;
      m_Updater.DownloadCompletedDelegate += DownloadCompleted;

      txtUrl.Text = "http://www.sakya.it/updater/updater.php";
      txtAppName.Text = "wpfMpdClient";

      btnDownload.IsEnabled = false;
    }

    private void Button_Click(object sender, RoutedEventArgs e)
    {
      if (string.IsNullOrEmpty(txtUrl.Text) || string.IsNullOrEmpty(txtAppName.Text)){
        MessageBox.Show(this, "Insert a url and a application name", "Error", MessageBoxButton.OK, MessageBoxImage.Warning);
        return;
      }

      btnCheck.IsEnabled = false;

      m_Updater.Url = new Uri(txtUrl.Text);
      m_Updater.AppName = txtAppName.Text;
      m_Updater.Check();

      txtOutput.Clear();
      txtOutput.AppendText("Checking application version...\r\n");
    }

    private void CheckCompleted(UpdaterApp app)
    {
      m_App = app;
      Dispatcher.BeginInvoke(new Action(() =>
      {
        btnCheck.IsEnabled = true;        

        if (m_App != null) {
          btnDownload.IsEnabled = true;
          txtOutput.AppendText(string.Format("Name: {0}\r\n", app.Name));
          txtOutput.AppendText(string.Format("Version: {0}\r\n", app.Version));
          txtOutput.AppendText(string.Format("File name: {0}\r\n", app.FileName));
          txtOutput.AppendText(string.Format("Released: {0}\r\n", app.ReleaseDate));
          txtOutput.AppendText(string.Format("Url: {0}\r\n", app.Url));
          txtOutput.AppendText("Changelog:\r\n");
          txtOutput.AppendText(string.Format("{0}\r\n", app.Changelog));
          txtOutput.AppendText("Message:\r\n");
          txtOutput.AppendText(string.Format("{0}\r\n", app.Message));
        } else {
          txtOutput.AppendText("Application not found\r\n");
        }
      }));
    }

    private void btnDownload_Click(object sender, RoutedEventArgs e)
    {
      if (m_App != null) {
        btnDownload.IsEnabled = false;
        m_Updater.Download(m_App.Url, string.Format("{0}\\temp\\{1}", Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), m_App.FileName));
      }
    }

    private void Download(string filename, double percentage)
    {
      pgbProgress.Maximum = 100;
      pgbProgress.Value = percentage;
    }

    private void DownloadCompleted(string filename)
    {
      btnDownload.IsEnabled = true;
      ProcessStartInfo psInfo = new ProcessStartInfo(filename);
      psInfo.UseShellExecute = true;
      Process process = Process.Start(psInfo);
      App.Current.Shutdown();
    }
  }
}
